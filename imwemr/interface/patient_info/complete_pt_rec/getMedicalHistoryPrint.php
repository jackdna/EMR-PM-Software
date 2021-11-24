<?php 
	if(!is_array($GLOBALS) && !isset($GLOBALS)){
		$ignoreAuth = true;
		include_once( '../../interface/globals.php' );
	}
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	// Functions Start ---------- 
	
	//Creates table heading rows for the provided array
	function getHtmlHeader($arrData = array(), $setTitle = false, $title = '', $callFrom = ''){
		if(count($arrData) == 0) return ;
		$widthArr = getWidth($callFrom);
		$returnStr = '';
		
		if(count($arrData) > 0){
			$counter = 1;
			foreach($arrData as $key => $val){
				if($counter == 1){
					$returnStr .= '<tr>';
				}
				$widthVal = $widthArr[$key];
				$returnStr .= '<th style="width:'.$widthVal.'px" class="text_lable bdrbtm bdrtop bdrlft bdrrght">'.$key.'</th>';
				
				if($counter == count($arrData)){
					$returnStr .= '</tr>';
				}
				$counter++;
			}
		}
		if($setTitle === true && empty($title) == false){
			$colString = '<tr><th class="tb_headingHeader11 pd5" colspan="'.count($arrData).'"><strong>'.$title.'</strong></th></tr>';
			$returnStr = $colString.$returnStr;
		}
		
		return $returnStr;
	}
	
	//Creates table data rows for the provided array
	function getHtmlBody($arrData = array(), $callFrom = ''){
		if(count($arrData) == 0) return ;
		
		$widthArr = getWidth($callFrom);
		
		$returnStr = '';
		foreach($arrData as $key_parent => $val_parent){
			$counter = 1;
			
			foreach($val_parent as $key => $val){
				if($counter == 1){
					$returnStr .= '<tr>';
				}
				
				if(is_array($val)){
					$val = implode(', ',$val);
				}
				
				$widthVal = $widthArr[$key];
				
				if($callFrom == 'MedicationsOcu' || $callFrom == 'MedicationsSys'){
					if($key == 'MedName' || $key == 'Comments' || $key = 'Sig') $val = wordwrap($val, 10, '<br>', true);
				}
				
				$returnStr .= '<td style="width:'.$widthVal.'px" class="bdrbtm bdrlft bdrrght">'.$val.'</td>';
				
				if($counter == count($val_parent)){
					
					$returnStr .= '</tr>';
				}
				
				$returnStr = $returnStr;
				$counter++;
			}
		}
		return $returnStr;
	}
	
	//Returns width for the sections in Review of System document
	function getWidth($call = ''){
		$returnArr = array();
		switch($call){
			case 'MedicationsSys':
				$returnArr['MedName'] = 105;
				$returnArr['Sig'] = 50;
				$returnArr['Dosage'] = 85;
				$returnArr['BeginDate'] = 100;
				$returnArr['EndDate'] = 100;
				$returnArr['Comments'] = 100;
				$returnArr['Code'] = 50;
				$returnArr['Status'] = 70;
			break;
			
			case 'MedicationsOcu':
				$returnArr['MedName'] = 90;
				$returnArr['Sig'] = 50;
				$returnArr['Dosage'] = 100;
				$returnArr['BeginDate'] = 85;
				$returnArr['EndDate'] = 100;
				$returnArr['Comments'] = 90;
				$returnArr['Code'] = 50;
				$returnArr['Status'] = 50;
				$returnArr['Site'] = 35;
			break;
			
			case 'Allergies':
				$returnArr['Name'] = 150;
				$returnArr['BeginDate'] = 100;
				$returnArr['Comments'] = 150;
				$returnArr['Type'] = 100;
				$returnArr['Status'] = 100;
				$returnArr['Code'] = 90;
			break;
			
			case 'ProceduresOcu':
				$returnArr['Name'] = 210;
				$returnArr['Code'] = 100;
				$returnArr['ProcedureDate'] = 100;
				$returnArr['Status'] = 100;
				$returnArr['Type'] = 100;
				$returnArr['Site'] = 82;
			break;
			
			case 'ProceduresSys':
				$returnArr['Name'] = 210;
				$returnArr['Code'] = 100;
				$returnArr['ProcedureDate'] = 150;
				$returnArr['Status'] = 100;
				$returnArr['Type'] = 140;
			break;	
				
			case 'OcularPt':
				//$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 360;
				$returnArr['Description'] = 365;
				//$returnArr['Description'] = 327;
				//$returnArr['LastModifiedDate'] = 100;
			break;
			
			case 'OcularRel':
				//$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 200;
				$returnArr['Description'] = 210;
				$returnArr['Relations'] = 305;
				//$returnArr['Relations'] = 285;
				//$returnArr['LastModifiedDate'] = 100;
			break;
			
			case 'GeneralPt':
				//$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 180;
				$returnArr['Description'] = 185;
				$returnArr['UnderControl'] = 222;
				$returnArr['Other'] = 120;
			break;
			
			case 'GeneralRel':
				//$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 180;
				$returnArr['Description'] = 185;
				$returnArr['Relation'] = 222;
				$returnArr['Other'] = 120;
			break;	
			
			case 'ReviewOfSystem':
				$returnArr['Name'] = 365;
				$returnArr['Values'] = 365;
			break;
			
			case 'BloodSugar':
				$returnArr['Description'] = 200;
				$returnArr['SugarValue'] = 150;
				$returnArr['Time'] = 117;
				$returnArr['Unit'] = 100;
				$returnArr['Fasting'] = 150;
			break;
			
			case 'Cholesterol':
				$returnArr['Description'] = 217;
				$returnArr['Total'] = 150;
				$returnArr['Triglycerides'] = 100;
				$returnArr['LDL'] = 100;
				$returnArr['HDL'] = 150;
			break;	
			
			case 'Immunizations':
				$returnArr['Date'] = 70;
				$returnArr['CvxCode'] = 50;
				$returnArr['Description'] = 127;
				$returnArr['Manufacturer'] = 90;
				$returnArr['Unit'] = 40;
				$returnArr['Site'] = 50;
				$returnArr['Notes'] = 85;
				$returnArr['RefusalReason'] = 100;
				$returnArr['Status'] = 50;
			break;
			
			case 'PatientProblems':
				$returnArr['ProblemName'] = 315;
				$returnArr['ProblemCode'] = 100;
				$returnArr['OnSetDate'] = 100;
				$returnArr['Status'] = 100;
				$returnArr['Type'] = 90;
			break;
			
			case 'EducationMaterial':
				$returnArr['Name'] = 420;
				$returnArr['Create Date'] = 160;
				$returnArr['Create Time'] = 160;
			break;
			
		}
		return $returnArr;
	}
	
	
	// To decide whether to show called section or not in PDF
	function showThis($key = ''){
		$return = false;
		if(empty($key)) return $return;
		
		//View sections in PDF
		$sectionArr = array(
			'Medications' => 1,
			'Allergies' => 1,
			'Procedures' => 1,
			'Ocular' => 1,
			'GeneralHealth' => 1,
			'BloodSugar' => 1,
			'Cholesterol' => 1,
			'SocialHistory' => 1,
			'Immunizations' => 1,
			'PatientProblems' => 1,
			'ReviewOfSystem' => 1,
			'EducationMaterial' => 1
		);
		
		if(isset($sectionArr[$key]) && empty($sectionArr[$key]) == false){
			$return = ($sectionArr[$key] == 1) ? true : false;
		}
		return $return;}
	
	
	function getMedOcularData($response = ''){
		if(empty($response)) return;
		$return_arr = array();
		$field_arr = getPtRelVal($response["PtCondition"],'~|~');
		$description_arr = getPtRelVal($response["ChronicDescriptions"],'~|~',true);
		$relatives_arr = getFieldVal($response["chronicRelatives"]);
		foreach($field_arr as $key => $val){
			foreach($val as $val_key => $value){
				$tmp_arr = array();
				$val_key = ($val_key == 0) ? 'other' : $val_key; 
				//$tmp_arr['ProblemId'] = $response['OcularId'].$value['id'];
				$tmp_arr['Name'] = $value['name'];
				$tmp_arr['Description'] = $description_arr[$key][$val_key];
				if($key == 'Relatives'){
					$relation_str = explode(',',$relatives_arr[$val_key]);
					$relation_str = array_filter($relation_str,'strlen');
					$tmp_arr['Relations'] = $relation_str;
				}
				if(isset($response['ModifiedDate']) && empty($response['ModifiedDate']) == false) $tmp_arr['LastModifiedDate'] = $response['ModifiedDate'];
				//$tmp_arr['LastModifiedDate'] = $response['ModifiedDate'];
				$return_arr[$key][] = $tmp_arr;
			}
		}
		return $return_arr;
	}
	
	//Returns array of patient and relative problems
	function getPtRelVal($dbValue = '',$delimiter = "~|~",$view = false){
		$dbValue 	= trim($dbValue);	
		$delimiter	= trim($delimiter);
		$return_arr = array();
		
		if(stristr($dbValue,$delimiter)){
			list($pat,$rel) = explode($delimiter,$dbValue);
			if($view == true){
				if(empty($pat) == false){
					$return_arr['Patient'] = getFieldVal($pat);
				}
				if(empty($rel) == false){
					$return_arr['Relatives'] = getFieldVal($rel);
				}
			}else{
				if(empty($pat) == false){
					$return_arr['Patient'] = getFieldsName($pat,1,1);
				}
				if(empty($rel) == false){
					$return_arr['Relatives'] = getFieldsName($rel,2,1);
				}
			}
			
		}
		return $return_arr;
	}
	
	//Returns field name of provided string
	function getFieldsName($str = '',$fields_for = 1,$section = 1,$view = false){
		$return_arr = array();
		if(empty($str)) return;
		if(stristr($str, ",")) {		//If String
			$val_arr = explode(',',$str);
			$val_arr = array_filter($val_arr,'strlen');
			$str = '"'.implode('", "',$val_arr).'"';
		}
		
		if(is_array($str)){				//If Array
			$str = '"'.implode('", "',$str).'"';
		}
		
		$qry = "SELECT id,field_key,name FROM fmh_med_fields WHERE field_key IN (".$str.") AND type = ".$fields_for." AND sec_id = ".$section."";
		$res = imw_query($qry);
		if($res && imw_num_rows($res) > 0){
			while($row = imw_fetch_assoc($res)){
				$tmp_arr = array();
				$tmp_arr['name'] = $row['name'];
				$tmp_arr['id'] = $row['id'];
				$return_arr[$row['field_key']] = $tmp_arr;
			}
		}
		
		return $return_arr;
	}
	
	//Returns Fields value
	function getFieldVal($str = ''){
		$return_arr = array();
		if(empty($str)) return;
		
		$strSep="~!!~~";
		$strSep2=":*:";
		
		$arrDescTmp = explode($strSep, $str);
		$arrDescTmp = array_filter($arrDescTmp,'strlen');
		if(count($arrDescTmp) > 0)
		{
			foreach($arrDescTmp as $key => $val)
			{
				$arrTmp = explode($strSep2,$val);
				$fId = $arrTmp[0];
				$return_arr[$fId] = $arrTmp[1];
			}
		}
		return $return_arr;
	}
	
	//Returns Gen. Health Data
	function getGenHealth($data = array()){
		if(count($data) == 0) return false;
		
		$rowId = $data['general_id'];
		/*Patient Medical conditions Description Keys*/
		$medDescKeys = array(1=>'desc_high_bp', 2=>'desc_heart_problem', 7=>'desc_arthrities', 4=>'desc_lung_problem', 5=>'desc_stroke', 6=>'desc_thyroid_problems', 3=>'desc_u', 13=>'desc_LDL', 8=>'desc_ulcers', 14=>'desc_cancer');
		
		/*Relatives - Relation Keys*/
		$medRelKeys = array(1=>'relDescHighBp', 2=>'relDescHeartProb', 7=>'relDescArthritisProb', 4=>'relDescLungProb', 5=>'relDescStrokeProb', 6=>'relDescThyroidProb', 3=>'desc_r', 13=>'relDescLDL', 8=>'relDescUlcersProb', 14=>'relDescCancerProb');
		
		/*Elements Where Sub Fields Exists*/
		$subFieldsPt = array(7);
		$subFieldVal = array( 7=>array('7.1'=>'RA', '7.2'=>'OA') );	
			
		/*Patient Medical Conditions Container*/
		$ptMedContidions = array();
		
		/*Patient Medical Conditions Container*/
		$relMedContidions = array();

		/*field Keys*/
				/*Patient Medical conditions*/
				$ptCondiftionsKeys = array(); 
				$sqlKey = 'SELECT `id`, `name`, `field_key` FROM `fmh_med_fields` WHERE `type`=1 AND `sec_id`=2';
				$respKey = imw_query($sqlKey);
				while( $field_row = imw_fetch_assoc($respKey) )
				{
					$fieldKey = array_pop($field_row);
					$ptCondiftionsKeys[$fieldKey] = $field_row;
				}
				
				/*Relatives Medical Conditions*/
				$relCondiftionsKeys = array(); 
				$sqlKey = 'SELECT `id`, `name`, `field_key` FROM `fmh_med_fields` WHERE `type`=2 AND `sec_id`=2';
				$respKey = imw_query($sqlKey);
				while( $med_field_row = imw_fetch_assoc($respKey) )
				{
					$fieldKey = array_pop($med_field_row);
					$relCondiftionsKeys[$fieldKey] = $med_field_row;
				}
		/*End field Keys*/
				
		/*Patient Medical Condition Values*/
		$ptMedCondVals = explode(',', $data['any_conditions_you']);
		$ptMedCondVals = array_filter($ptMedCondVals);
		
		/*Relatives Medical Condition Values*/
		$relMedCondVals = explode(',', $data['any_conditions_relative']);
		$relMedCondVals = array_filter($relMedCondVals);
		
		/*Under Control*/
		$ptMedUC = explode('~|~' , $data['chk_under_control']);
		$ptMedUC = array_shift($ptMedUC);
		$ptMedUC = explode(',', $ptMedUC);
		$ptMedUC = array_filter($ptMedUC);
		
		/*subFieldValues*/
		$ptSubFields0 = explode('~|~' , $data['sub_conditions_you']);
		$ptSubFields = array_shift($ptSubFields0);
		$relSubFields = array_pop($ptSubFields0);
		
		$ptSubFields = explode(',', $ptSubFields);
		$ptSubFields = array_filter($ptSubFields);
		
		$relSubFields = explode(',', $relSubFields);
		$relSubFields = array_filter($relSubFields);
		
		/*Process Relatives Medical conditions*/
		foreach( $ptMedCondVals as $ptMedCondVal )
		{
			$ptMedCondVal = (int)$ptMedCondVal;
			
			$condition = array();
			//$condition['ProblemId'] = $rowId.$ptCondiftionsKeys[$ptMedCondVal]['id'];
			$condition['Name'] = $ptCondiftionsKeys[$ptMedCondVal]['name'];
			
			/*Field Description*/
			$desc = explode('~|~', $data[ $medDescKeys[$ptMedCondVal] ]);
			$condition['Description'] = trim(array_shift($desc));
			
			/*Check Under Controle*/
			$condition['UnderControl'] = isset( $ptMedUC[$ptMedCondVal] );
			
			/*Container for Sub Fields*/
			$condition['Other'] = array();
			
			/*Sub Fields*/
			if( isset($subFieldVal[$ptMedCondVal]) )
			{
				foreach($ptSubFields as $ptSubField)
				{
					if( isset($subFieldVal[$ptMedCondVal][$ptSubField]) )
					{
						array_push($condition['Other'], $subFieldVal[$ptMedCondVal][$ptSubField]);
					}
				}
			}
			
			
			/*Insert diabetise Type*/
			if( $ptMedCondVal === 3)
			{
				$diabValues = explode('~|~', $data['diabetes_values']);
				$diabValues = array_shift($diabValues);
				$diabValues = explode(',', $diabValues);
				$diabValues = array_filter($diabValues);
				
				$condition['Other'] = $diabValues;
			}
			
			array_push($ptMedContidions, $condition);
		}
		
		/*Process Patient Medical conditions*/
		foreach( $relMedCondVals as $relMedCondVal )
		{
			$relMedCondVal = (int)$relMedCondVal;
			
			$condition = array();
			//$condition['ProblemId'] = $rowId.$relCondiftionsKeys[$relMedCondVal]['id'];
			$condition['Name'] = $relCondiftionsKeys[$relMedCondVal]['name'];
			
			/*Field Description*/
			$desc = explode('~|~', $data[ $medDescKeys[$relMedCondVal] ]);
			$condition['Description'] = trim(array_shift($desc));
			
			/*Relative*/
			$rel = explode('~|~', $data[ $medRelKeys[$relMedCondVal] ]);
			$rel = array_shift($rel);
			$rel = explode(',', $rel);
			$rel = array_filter($rel);
			
			$condition['Relation'] = $rel;
			
			/*Check Under Controle* /
			$condition['Under Control'] = isset( $relMedUC[$relMedCondVal] );/**/
			
			/*Container for Sub Fields*/
			$condition['Other'] = array();
			
			/*Sub Fields*/
			if( isset($subFieldVal[$relMedCondVal]) )
			{
				foreach($relSubFields as $relSubField)
				{
					if( isset($subFieldVal[$relMedCondVal][$relSubField]) )
					{
						array_push($condition['Other'], $subFieldVal[$relMedCondVal][$relSubField]);
					}
				}
			}
			
			
			/*Insert diabetise Type*/
			if( $relMedCondVal === 3)
			{
				$diabValues = explode('~|~', $data['diabetes_values']);
				$diabValues = array_shift($diabValues);
				$diabValues = explode(',', $diabValues);
				$diabValues = array_filter($diabValues);
				
				$condition['Other'] = $diabValues;
			}
			
			array_push($relMedContidions, $condition);
		}
		
		$return_data = array();
		
		if(count($ptMedContidions) > 0){
			$return_data['Patient'] = $ptMedContidions;
		}
		
		if(count($relMedContidions) > 0){
			$return_data['Relatives'] = $relMedContidions;
		}
		return $return_data;
	}
	
	function convertDate($date = '', $format = 'm-d-Y'){
		if(empty($date)) return ;
		
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
			$date = date($format, strtotime($date));
		}
		return $date;
	}
	
	// Functions End ---------- 
	
	$formId = (isset($chartNoteId) && empty($chartNoteId) == false) ? $chartNoteId : '';
	$patientId = (isset($_SESSION['patient']) && empty($_SESSION['patient']) == false) ? $_SESSION['patient'] : '';
	$pdf_body_str = '';
	if(empty($formId) == false){
		$medData = $tmp_arr = array();
		
		$arrSites = array(1 => 'OS', 2 => 'OD', 3 => 'OU', 4 => 'PO');
		$drugType = array('fdbATDrugName' => 'Drug', 'fdbATIngredient' => 'Ingredient', 'fdbATAllergenGroup' => 'Allergen');
		$procType = array("surgery" => "Surgery", "procedure" => "Procedure", "intervention" => "Intervention");
		
		$dataQry = imw_query("SELECT 
						* 
					FROM  
						chart_genhealth_archive 
					WHERE
						patient_id='".$patientId."' AND
						form_id = '".$formId."'");
		if($dataQry && imw_num_rows($dataQry) > 0){
			while($rowFetch = imw_fetch_assoc($dataQry)){
				/* Medications -- Allergies -- Sx/Procedures -- UDI */
				$arrList = unserialize($rowFetch['lists']);
				if(count($arrList) > 0){
					foreach($arrList as $arrData){
						foreach($arrData as $data){
							switch($data['type']){
								case 1:
								case 4:
								/*echo ($_SESSION['asPrint']? 'TRUE':'FALSE').'<br>';
								echo ((!$_SESSION['asPrint'] && strtolower($data['allergy_status']) != 'deleted') ? 'NOT SESSION':'FREE1').'----'.strtolower($data['allergy_status']).'<br>';
								echo (($_SESSION['asPrint'] && strtolower($data['allergy_status']) == 'active')? 'SESSION':'FREE2').'----'.strtolower($data['allergy_status']).'<br>';*/
									
									
									if((!$_SESSION['asPrint'] && strtolower($data['allergy_status']) != 'deleted') || 
										($_SESSION['asPrint'] && strtolower($data['allergy_status']) == 'active') ){
										unset($tmp_arr);
										$tmp_arr['MedName'] = $data['title'];
										$tmp_arr['Sig'] = $data['sig'];
										$tmp_arr['Dosage'] = $data['destination'];
										$tmp_arr['BeginDate'] = (empty($data['begdate']) == false && $data['begdate'] != "0000-00-00") ? convertDate($data['begdate']) : '';
										$tmp_arr['EndDate'] = (empty($data['enddate']) == false && $data['enddate'] != "0000-00-00") ? convertDate($data['enddate']) : '';
										$tmp_arr['Comments'] = $data['med_comments'];
										$tmp_arr['Code'] = (isset($data['ccda_code']) && $data['ccda_code'] != null) ? $data['ccda_code'] : '';
										$tmp_arr['Status'] = $data['allergy_status'];
										if($data['type'] == 1){
											$medData['Medications']['Systemic'][] = $tmp_arr;
										}else{
											$tmp_arr['Site'] = (isset($arrSites[$data['sites']]) && empty($arrSites[$data['sites']]) == false) ? $arrSites[$data['sites']] : '';
											$medData['Medications']['Ocular'][] = $tmp_arr;
										}
									}
								break;
								
								case 3:
								case 7:
									//if(strtolower($data['allergy_status']) == 'active' || strtolower($data['allergy_status']) == 'completed'){
									if((!$_SESSION['asPrint'] && (strtolower($data['allergy_status']) == 'active' || strtolower($data['allergy_status']) == 'completed') ) || 
										($_SESSION['asPrint'] && strtolower($data['allergy_status']) == 'active') ){	
										
										unset($tmp_arr);
										$tmp_arr['Name'] = $data['title'];
										$tmp_arr['BeginDate'] = (empty($data['begdate']) == false && $data['begdate'] != "0000-00-00") ? convertDate($data['begdate']) : '';
										$tmp_arr['Comments'] = $data['comments'];
										$tmp_arr['Type'] = $drugType[$data['ag_occular_drug']];
										$tmp_arr['Status'] = $data['allergy_status'];
										$tmp_arr['Code'] = $data['ccda_code'];
										
										$medData['Allergies'][] = $tmp_arr;
									}
								break;
								
								case 5:
								case 6:
									if(strtolower($data['allergy_status']) != 'deleted'){
										unset($tmp_arr);
										$tmp_arr['Name'] = $data['title'];
										$tmp_arr['Code'] = $data['ccda_code'];
										$tmp_arr['ProcedureDate'] = (empty($data['begdate']) == false && $data['begdate'] != "0000-00-00") ? convertDate($data['begdate']) : '';
										$tmp_arr['Status'] = $data['allergy_status'];
										$tmp_arr['Type'] = $procType[$data['proc_type']];
										
										if($data['type'] == 6){
											$tmp_arr['Site'] = (isset($arrSites[$data['sites']]) && empty($arrSites[$data['sites']]) == false) ? $arrSites[$data['sites']] : '';
											$medData['Procedures']['Ocular'][] = $tmp_arr;
										}else{
											$medData['Procedures']['Systemic'][] = $tmp_arr;
										}
									}
								break;	
								
								/* case 9:
									if(strtolower($data['allergy_status']) != 'deleted'){
										//$medData['UDI'][$data['parent_id']] = $data; //UDI
									}
								break; */
							}
							unset($tmp_arr);
						}
					}
				}
				
				/* Ocular health */
				$arrOcular = unserialize($rowFetch['ocular']);
				if(count($arrOcular) > 0){
					$arrTemp = array();
					if($arrOcular['any_conditions_others_you'] == 1){
						$arrTemp['any_conditions_you_new'] = '0'.$arrOcular['any_conditions_you'];
					}	
						
					if($arrOcular['any_conditions_other_relative'] == 1){
						$arrTemp['any_conditions_relative_new'] = '0'.$arrOcular['any_conditions_relative'];
					}	

					$arrTemp['OcularId'] = $arrOcular['ocular_id'];
					$arrTemp['PatientId'] = $arrOcular['patient_id'];
					$arrTemp['ChronicDescriptions'] = $arrOcular['chronicDesc'];
					$arrTemp['chronicRelatives'] = $arrOcular['chronicRelative'];
					//$arrTemp['ModifiedDate'] = date('m-d-Y', strtotime($arrOcular['timestamp']));
					$arrTemp['PtCondition'] = $arrTemp['any_conditions_you_new'].$arrTemp['any_conditions_relative_new'];
					
					if(count(getMedOcularData($arrTemp)) > 0){
						$medData['Ocular'] = getMedOcularData($arrTemp);
					}
				}
				
				/* General Health */
				$arrGenMed = unserialize($rowFetch['general_medicine']);
				if(count($arrGenMed) > 0){
					/* Medical Conditions */
					$genData = getGenHealth($arrGenMed);
					if($genData !== false && count($genData) > 0){
						$medData['GeneralHealth'] = $genData;
					}
					
					/* Review Of System */
					$reviewSysArr = array();
					
					//Mapping Array to simplify the output
					$mainFieldsArr = array(
						7 => array('Name' => 'Allergic/Immunologic', 'Other' => 'review_aller_others'),
						4 => array('Name' => 'Cardiovascular', 'Other' => 'review_card_others'),
						1 => array('Name' => 'Constitutional', 'Other' => 'review_const_others'),
						2 => array('Name' => 'Ear, Nose, Mouth & Throat', 'Other' => 'review_head_others'),
						13 => array('Name' => 'Endocrine', 'Other' => 'review_endocrine_others'),
						14 => array('Name' => 'Eyes', 'Other' => 'review_eye_others'),
						5 => array('Name' => 'Gastrointestinal', 'Other' => 'review_gastro_others'),
						6 => array('Name' => 'Genitourinary', 'Other' => 'review_genit_others'),
						11 => array('Name' => 'Hemotologic/Lymphatic', 'Other' => 'review_blood_lymph_others'),
						9 => array('Name' => 'Integumentary', 'Other' => 'review_intgmntr_others'),
						12 => array('Name' => 'Musculoskeletal', 'Other' => 'review_musculoskeletal_others'),
						8 => array('Name' => 'Neurological', 'Other' => 'review_neuro_others'),
						10 => array('Name' => 'Psychiatry', 'Other' => 'review_psychiatry_others'),
						3 => array('Name' => 'Respiratory', 'Other' => 'review_resp_others')
					);
					
					//Sub fields for Main Fields
					$fieldsArr = array(
						7 => array(
							1 => 'Seasonal Allergies',
							2 => 'Hay Fever'
						),						
						4 => array(
							1 => 'Chest Pain',
							2 => 'Congestive Heart Failure',
							3 => 'Irregular Heart beat',
							4=> 'Shortness of Breath',
							5=> 'High Blood Pressure',
							6=> 'Low Blood Pressure',
							7=> 'Pacemaker/defibrillator'
						),
						1 => array(
							1 => 'Fever',
							2 => 'Weight Loss',
							3 => 'Rash',
							4 => 'Skin Disease',
							4 => 'Fatigue'
						),
						2 => array(
							1 => 'Sinus Infection',
							2 => 'Post Nasal Drips',
							3 => 'Runny Nose',
							4 => 'Dry Mouth',
							5 => 'Deafness'
						),
						13 => array(
								1 => "Mood Swings",
								2 => "Constipation",
								3 => "Polydipsia",
								4 => "Hypothyroidism",
								5 => "Hyperthyroidism"
							),
						14 => array(
								1 => "Vision loss",
								2 => "Eye pain",
								3 => "Double vision",
								4 => "Headache"
							),		
						5 => array(
							1 => 'Vomiting',
							2 => 'Ulcers',
							3 => 'Diarrhea',
							4 => 'Bloody Stools',
							5 => 'Hepatitis',
							6 => 'Jaundice',
							7 => "Constipation"
						),
						6 => array(
							1 => 'Genital Ulcers',
							2 => 'Discharge',
							3 => 'Kidney Stones',
							4 => 'Blood in Urine'
							
						),
						11 => array(
								1 => "Anemia",
								2 => "Blood Transfusions",
								3 => "Excessive Bleeding",
								4 => "Purpura",
								5 => "Infection"
							),
						9 => array(
								1 => "Rashes",
								2 => "Wounds",
								3 => "Breast Lumps",
								4 => "Eczema",
								5 => "Dermatitis"
							),
						12 => array(
								1 => "Pain",
								2 => "Joint Ache",
								3 => "Stiffness",
								4 => "Swelling",
								5 => "Paralysis Fever"
							),		
						
						8 => array(
							1 => 'Headache',
							2 => 'Migraines',
							3 => 'Paralysis Fever',
							4 => 'Joint Ache',
							5 => "Seizures",
							6 => "Numbness",
							7 => "Faints",
							8 => "Stroke",
							9 => "Multiple Sclerosis",
							10 => "Alzheimer's Disease",
							11 => "Parkinson's Disease",
							12 => "Dementia"
						),
						10 => array(
								1 => "Depression",
								2 => "Anxiety",
								3 => "Paranoia",
								4 => "Sleep Patterns",
								5 => "Mental and/or emotional factors",
								6 => "Alzheimer's Disease",
								7 => "Parkinson's disease",
								8 => "Memory Loss"
								
							),
						3 => array(
							1 => 'Cough',
							2 => 'Bronchitis',
							3 => 'Shortness of Breath',
							4 => 'Asthma',
							5 => 'Emphysema',
							6 => 'COPD',
							7 => 'TB'
						),
					);
					
					//Disable Column Array
					$negColArr = array();
					if(isset($arrGenMed['negChkBx']) && empty($arrGenMed['negChkBx']) == false) $negColArr = array_filter(explode(',', $arrGenMed['negChkBx']));
					
					if(count($mainFieldsArr) > 0){
						foreach($mainFieldsArr as $fieldKey => $fieldParam){
							$tmpArr = array();
							
							//If disable column has current field value in it than set the array and skip the rest of the conditions
							if(in_array($fieldKey, $negColArr)){
								$tmpArr['Name'] = $fieldParam['Name'];
								$tmpArr['Values'] = 'Negative';	
							}
							
							if(count($tmpArr) == 0){
								//Other Field value
								$otherVal = (isset($arrGenMed[$fieldParam['Other']]) && empty($arrGenMed[$fieldParam['Other']]) == false) ? $arrGenMed[$fieldParam['Other']] : '';
								
								//Main Field Value
								$dbField = str_replace('_others', '', $fieldParam['Other']);
								$fieldVal = (isset($arrGenMed[$dbField]) && empty($arrGenMed[$dbField]) == false) ? array_filter(explode(',', $arrGenMed[$dbField])) : '';
								
								$subFieldsVal = '';
								if(is_array($fieldVal) === true && count($fieldVal) > 0){
									$tmpSubArr = array();
									foreach($fieldVal as $subFields){
										if(isset($fieldsArr[$fieldKey][$subFields])){
											array_push($tmpSubArr, $fieldsArr[$fieldKey][$subFields]);
										}
									}
									if(count($tmpSubArr) > 0) $subFieldsVal = implode(', ', $tmpSubArr);
								}
								
								if(empty($subFieldsVal) == false && empty($otherVal) == false) $subFieldsVal .= ','.$otherVal;
								
								$tmpArr['Name'] = $fieldParam['Name'];
								$tmpArr['Values'] = $subFieldsVal;
							}
							
							array_push($reviewSysArr, $tmpArr);
						}
					}
					
					if(count($reviewSysArr) > 0) $medData['ReviewOfSystem'] = $reviewSysArr;
				}
				
				/* Patient Blood Sugar */
				$arrBloodSugar = unserialize($rowFetch['patient_blood_sugar']);
				if(count($arrBloodSugar) > 0){
					$tmp_arr = array();
					$counter = 0;
					foreach($arrBloodSugar as $obj){
						if($counter == 0){
							$arr_tmp = array();
							$create_date = $create_time = '';
							$creation_date = strtotime($obj['creation_date']);
							list($create_date,$create_time) = explode('||', date('Y-m-d||Gi.s', $creation_date));
							
							$arr_tmp['Description'] = $obj['description'];
							$arr_tmp['SugarValue'] = $obj['sugar_value'];
							$arr_tmp['Time'] = $obj['time_of_day'];
							$arr_tmp['Unit'] = 'mg/dl';
							$arr_tmp['Fasting'] = ($obj['is_fasting'] == 1) ? 'Yes' : 'No';
							
							$tmp_arr[$create_date][] = $arr_tmp;
						}
						$counter++;
					}
					
					if(count($tmp_arr) > 0){
						$medData['BloodSugar'] = reset($tmp_arr);
					}
				}
				
				/* Patient Cholesterol */
				$arrPtCholesterol = unserialize($rowFetch['patient_cholesterol']);
				if(count($arrPtCholesterol) > 0){
					$tmp_arr = array();
					$counter = 0;
					foreach($arrPtCholesterol as $obj){
						if($counter == 0){
							$arr_tmp = array();
							$create_date = $create_time = '';
							$creation_date = strtotime($obj['creation_date']);
							list($create_date,$create_time) = explode('||', date('m-d-Y||Gi.s', $creation_date));
							
							$arr_tmp['Description'] = $obj['description'];
							$arr_tmp['Total'] = $obj['cholesterol_total'];
							$arr_tmp['Triglycerides'] = $obj['cholesterol_triglycerides'];
							$arr_tmp['LDL'] = $obj['cholesterol_LDL'];
							$arr_tmp['HDL'] = $obj['cholesterol_HDL'];
							
							$tmp_arr[$create_date][] = $arr_tmp;
						}
						$counter++;
					}
					$medData['Cholesterol'] = reset($tmp_arr);
				}
				
				/* Social History */
				$arrSocialHistory = unserialize($rowFetch['social_history']);
				if(count($arrSocialHistory) > 0){
					$status = $code = '';
					$tmp_arr = array();
					list($status,$code) = explode('/',$arrSocialHistory['smoking_status']);
					$tmp_arr['SmokingStatus'] = trim($status);
					$tmp_arr['SatusCode'] = trim($code);
					$medData['SocialHistory'] = $tmp_arr;
				}
				
				/* Immunizations */
				$arrImmunizations = unserialize($rowFetch['immunizations']);
				if(count($arrImmunizations) > 0){
					$tmp_arr = array();
					
					foreach($arrImmunizations as $obj){
						$arr_tmp = array();
						$immu_id = $immu_text = '';
						$arr_tmp['Date'] = (empty($obj['administered_date']) == false && $obj['administered_date'] != '0000-00-00') ? convertDate($obj['administered_date']) : '';
						list($immu_id,$immu_text) = explode('-',$obj['immunization_id'],2);
						$arr_tmp['CvxCode'] = $obj['immunization_cvx_code'];
						$arr_tmp['Description'] = trim($immu_text);
						$arr_tmp['Manufacturer'] = $obj['manufacturer'];
						$arr_tmp['Unit'] = $obj['immzn_dose_unit'];
						$arr_tmp['Site'] = $obj['site'];
						$arr_tmp['Notes'] = $obj['note'];
						$arr_tmp['RefusalReason'] = $obj['refusal_reason'];
						$arr_tmp['Status'] = $obj['status'];
						
						$tmp_arr[] = $arr_tmp;
					}
					$medData['Immunizations'] = $tmp_arr;
				}
				
				/* Pt. Problem List */
				$arrPtProblem = unserialize($rowFetch['pt_problem_list']);
				if(count($arrPtProblem) > 0){
					foreach($arrPtProblem as $obj){
						//if($obj['status'] != 'Deleted'){
						if( (!$_SESSION['asPrint'] && strtolower($obj['status']) <> 'deleted') ||
							($_SESSION['asPrint'] && strtolower($obj['status']) == 'active') ){
											
							$tmp_arr = array();
							$tmp_arr['ProblemName'] = $obj['problem_name'];
							$tmp_arr['ProblemCode'] = $obj['ccda_code'];
							$tmp_arr['OnSetDate'] = convertDate($obj['onset_date']);
							$tmp_arr['Status'] = $obj['status'];
							$tmp_arr['Type'] = $obj['prob_type'];
							$medData['PatientProblems'][] = $tmp_arr;
						} 
					}
				}
			}	
		}
		
		//Patient Education Material
		$chkEdu = imw_query("SELECT name as Name, DATE(date_time) as 'Create Date', TIME(date_time) as 'Create Time'  FROM document_patient_rel WHERE p_id = '".$patientId."' AND doc_id != 0 AND status = 0 AND form_id = '".$formId."' ");
		
		if($chkEdu && imw_num_rows($chkEdu) > 0){
			while($rowFetch = imw_fetch_assoc($chkEdu)){
				$rowFetch['Create Date'] = ($rowFetch['Create Date'] !== '0000-00-00' && empty($rowFetch['Create Date']) == false) ? date('m-d-Y', strtotime($rowFetch['Create Date'])) : '';
				$medData['EducationMaterial'][] = $rowFetch;
			}
		}
		
		if(count($medData) > 0){
			$arrData = $medData;
			//Medications
			if(isset($arrData['Medications']) && count($arrData['Medications']) > 0 && showThis('Medications')){
				$bodyStr = $ocuStr = $sysStr = $td_wrap = '';
				$counter = 0;
				foreach($arrData['Medications'] as $key => $val){
					switch($key){
						case 'Systemic':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'Medications - '.$key.'', 'MedicationsSys');
							}
							$sysStr .= $headerStr;
							$sysStr .= getHtmlBody($val, 'MedicationsSys');
							$counter = 0;
						break;
						
						case 'Ocular':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'Medications - '.$key.'', 'MedicationsOcu');
							}
							$ocuStr .= $headerStr;
							$ocuStr .= getHtmlBody($val, 'MedicationsOcu');
							$counter = 0;
						break;
					}
				}	
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$sysStr.'</table></td></tr>';
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$ocuStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
			//Allergies
			if(isset($arrData['Allergies']) && count($arrData['Allergies']) > 0  && showThis('Allergies')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['Allergies']), true, 'Allergies', 'Allergies');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['Allergies'], 'Allergies');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
			
			//Procedures
			if(isset($arrData['Procedures']) && count($arrData['Procedures']) > 0 && showThis('Procedures')){
				$bodyStr = $sysStr = $ocuStr = $td_wrap = '';
				$counter = 0;
				foreach($arrData['Procedures'] as $key => $val){
					switch($key){
						case 'Systemic':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'Procedures - '.$key.'', 'ProceduresSys');
							}
							$sysStr .= $headerStr;
							$sysStr .= getHtmlBody($val, 'ProceduresSys');
							$counter = 0;
						break;
						
						case 'Ocular':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'Procedures - '.$key.'', 'ProceduresOcu');
							}
							$ocuStr .= $headerStr;
							$ocuStr .= getHtmlBody($val, 'ProceduresOcu');
							$counter = 0;
						break;
					}
				}	
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$sysStr.'</table></td></tr>';
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$ocuStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;		
			}
			
			//Ocular
			if(isset($arrData['Ocular']) && count($arrData['Ocular']) > 0 && showThis('Ocular')){
				$relStr = $patRel = $td_wrap = '';
				$counter = 0;
				foreach($arrData['Ocular'] as $key => $val){
					switch($key){
						case 'Patient':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'Ocular - '.$key.'', 'OcularPt');
							}
							$patRel .= $headerStr;
							$patRel .= getHtmlBody($val, 'OcularPt');
							$counter = 0;
						break;
						
						case 'Relatives':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'Ocular - '.$key.'', 'OcularRel');
							}
							$relStr .= $headerStr;
							$relStr .= getHtmlBody($val, 'OcularRel');
							$counter = 0;
						break;
					}
				}
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$patRel.'</table></td></tr>';
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$relStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;		
			}
			
			//GeneralHealth
			if(isset($arrData['GeneralHealth']) && count($arrData['GeneralHealth']) > 0 && showThis('GeneralHealth')){
				$relStr = $patRel = $td_wrap = '';
				$counter = 0;
				foreach($arrData['GeneralHealth'] as $key => $val){
					switch($key){
						case 'Patient':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'General Health - '.$key.'', 'GeneralPt');
							}
							$patRel .= $headerStr;
							$patRel .= getHtmlBody($val, 'GeneralPt');
							$counter = 0;
						break;
						
						case 'Relatives':
							$headerStr = '';
							if($counter == 0){
								$headerStr = getHtmlHeader(reset($val), true, 'General Health - '.$key.'', 'GeneralRel');
							}
							$relStr .= $headerStr;
							$relStr .= getHtmlBody($val, 'GeneralRel');
							$counter = 0;
						break;
					}
				}	
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$patRel.'</table></td></tr>';
				$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$relStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;		
			}
			
			//Review Of System
			if(isset($arrData['ReviewOfSystem']) && count($arrData['ReviewOfSystem']) > 0 && showThis('ReviewOfSystem')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['ReviewOfSystem']), true, 'Review Of System', 'ReviewOfSystem');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['ReviewOfSystem'], 'ReviewOfSystem');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
			//BloodSugar
			if(isset($arrData['BloodSugar']) && count($arrData['BloodSugar']) > 0 && showThis('BloodSugar')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['BloodSugar']), true, 'Blood Sugar', 'BloodSugar');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['BloodSugar'], 'BloodSugar');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
			//Cholesterol
			if(isset($arrData['Cholesterol']) && count($arrData['Cholesterol']) > 0 && showThis('Cholesterol')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['Cholesterol']), true, 'Cholesterol', 'Cholesterol');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['Cholesterol'], 'Cholesterol');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;		
			}
			
			//SocialHistory
			if(isset($arrData['SocialHistory']) && count($arrData['SocialHistory']) > 0 && showThis('SocialHistory')){
				$bodyStr = $td_wrap = '';
				
				$headerStr = getHtmlHeader(reset($arrData['SocialHistory']), true, 'Social History', 'SocialHistory');
				$headerStr = str_ireplace('1','2',$headerStr);
				$bodyStr .= $headerStr;
				if(isset($arrData['SocialHistory']['SmokingStatus']) && isset($arrData['SocialHistory']['SatusCode']) && empty($arrData['SocialHistory']['SmokingStatus']) == false && empty($arrData['SocialHistory']['SatusCode']) == false){
					$bodyStr .= '<tr><td style="width:370px" class="bdrbtm bdrlft bdrrght marginBot">'.$arrData['SocialHistory']['SmokingStatus'].'</td><td style="width:370px" class="bdrbtm bdrlft bdrrght">'.$arrData['SocialHistory']['SatusCode'].'</td></tr>';
					$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				}
				$pdf_body_str .= $td_wrap;		
			}
			
			//Immunizations
			if(isset($arrData['Immunizations']) && count($arrData['Immunizations']) > 0 && showThis('Immunizations')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['Immunizations']), true, 'Immunizations', 'Immunizations');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['Immunizations'], 'Immunizations');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
			//PatientProblems
			if(isset($arrData['PatientProblems']) && count($arrData['PatientProblems']) > 0 && showThis('PatientProblems')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['PatientProblems']), true, 'Problem List', 'PatientProblems');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['PatientProblems'], 'PatientProblems');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
			//EducationMaterial
			if(isset($arrData['EducationMaterial']) && count($arrData['EducationMaterial']) > 0 && showThis('EducationMaterial')){
				$bodyStr = '';
				
				$headerStr = getHtmlHeader(reset($arrData['EducationMaterial']), true, 'Given Education Material', 'PatientProblems');
				$bodyStr .= $headerStr;
				$bodyStr .= getHtmlBody($arrData['EducationMaterial'], 'EducationMaterial');
				
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
				$pdf_body_str .= $td_wrap;	
			}
			
		}
	}
	
	if(empty($pdf_body_str) == false) echo '<table style="width:700px;border-collapse:collapse;" border="0">'.str_replace('tb_headingHeader22','tb_headingHeader11',$pdf_body_str).'</table>';
	
?>