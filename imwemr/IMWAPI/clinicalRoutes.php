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
 * index.php
 * Access Type: InClude
 * Purpose: Routes for Demographic API calls.
*/

$patientId = 0;
	
/*Validate Patient ID*/
$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	$arrServ = array(0 => 'fatal', 1 => 'mild', 2 => 'mildtomoderate', 3 => 'moderate', 4 => 'moderatetosevere', 5 => 'severe');
	$service->__set('arrServ', $arrServ);
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	if($request->__isset('startDate') && trim($request->__get('startDate')) !== '' ){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
		$service->__set('startDate', $startDate);
		
/* 		if($request->__isset('endDate') == false){
			
			$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
			
		} */
	}
	
	if($request->__isset('endDate') && trim($request->__get('endDate')) !== '' ){
		$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
		
		$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
		$service->__set('endDate', $endDate);
		
		if($request->__isset('startDate') == false){
			
			$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
		}
	}
	
	if( empty($startDate) === false && empty($endDate) === true )
	{
		$endDate = $startDate;
	}
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(timestamp) BETWEEN '".$startDate."' AND '".$endDate."' ";
		$service->__set('whereCondition', $where);
	}	
	
	//Checking if patient has NKDA checked or not - No Common Medications
	$nkdaArr = array();
	$chkQry = $app->dbh->imw_query("select LOWER(module_name) as module from commonNoMedicalHistory where patient_id='".$patientId."' AND no_value = '' ");
	if($chkQry && $app->dbh->imw_num_rows($chkQry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkQry)){
			array_push($nkdaArr, $row['module']);
		}
	}
	
	if(count($nkdaArr) > 0){
		$service->__set('nkdaArr', $nkdaArr);
	}
});

/*Return Ocular Data*/
$this->get('/ocular', function($request, $response, $service,$app) use(&$patientId){
	$ocular_arr = array();
	$med_obj = new IMW\MEDHX($app->dbh,1);
	
	$where = $service->__get('whereCondition');
		/* Ocular Data */
		$ocular_qry = "
			SELECT 
				ocular_id as OcularId,
				patient_id as PatientId,
				CONCAT(any_conditions_you_new, any_conditions_relative_new)as PtCondition,
				chronicDesc as ChronicDescriptions,
				chronicRelative as chronicRelatives,
				DATE(timestamp) as ModifiedDate
			FROM
				(
					SELECT 
					ocular_id,patient_id,chronicDesc,chronicRelative,timestamp,
					if( any_conditions_others_you = 1, concat('0',any_conditions_you), any_conditions_you) as any_conditions_you_new,
					if( any_conditions_other_relative = 1, concat('0',any_conditions_relative), any_conditions_relative) as any_conditions_relative_new
					FROM 
						ocular 
					WHERE 
						patient_id = ".$patientId." 
						".$where."
				) A
		";	
		$res = $app->dbh->imw_query($ocular_qry);
		if($res && $app->dbh->imw_num_rows($res) > 0){
			while($row = $app->dbh->imw_fetch_assoc($res)){
				$tmp_data = $med_obj->getOcularData($row);
				$ocular_arr[] = $tmp_data;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		if(count($ocular_arr) == 0) $ocular_arr = 'No Ocular record';
		
		//Get Audit Data for the provided date
		// $startDate = $service->__get('startDate');
		// $endDate = $service->__get('endDate');
		// $auditArr = $med_obj->getAuditData('ocular', $patientId, $startDate, $endDate);
		// $response = array('Ocular' => $ocular_arr);
		// if($auditArr && is_array($auditArr) && count($auditArr) > 0){
		// 	$response['PreviousStatus'] = $auditArr;
		// }
		
		$response = array('Ocular' => $ocular_arr);
		return json_encode($response);
});

/*Return General Health Data*/
$this->get('/generalHealth', function($request, $response, $service, $app) use(&$patientId)  {
	$dbh = $app->dbh;
	$where = '';
	if( $service->__isset('startDate') && $service->__isset('endDate') ){
		$where = " AND `timestamp` BETWEEN '".$service->__get('startDate')." 00:00:00' AND '".$service->__get('endDate')." 23:59:59' ";
	}
	
	$sql = 'SELECT
				`general_id`,
				`any_conditions_you`,
				`any_conditions_relative`,
				`any_conditions_others_both`,
				`any_conditions_others`,
				`chk_under_control`,
				`diabetes_values`,
				`cbk_master_pt_con`,
				`cbk_master_fam_con`,
				`sub_conditions_you`,
				`desc_high_bp`, `desc_heart_problem`, `desc_arthrities`, `desc_lung_problem`, `desc_stroke`, `desc_thyroid_problems`, `desc_u`, `desc_LDL`, `desc_ulcers`,
				`relDescHighBp`, `relDescHeartProb`, `relDescArthritisProb`, `relDescLungProb`, `relDescStrokeProb`, `relDescThyroidProb`, `desc_r`, `relDescLDL`, `relDescUlcersProb`, `relDescCancerProb`,
				`desc_cancer`,
				DATE(`timestamp`) as timeStamp
			FROM
				`general_medicine`
			WHERE
				`patient_id`='.((int)$patientId).$where;
	//die;
	$resp = $dbh->imw_query($sql);
	
	/*Patient Medical Conditions Container*/
	$ptMedContidions = array();
	
	/*Patient Medical Conditions Container*/
	$relMedContidions = array();
	
	
	
	if( $resp && $dbh->imw_num_rows($resp) )
	{
		$data = $dbh->imw_fetch_assoc($resp);
		$med_obj = new IMW\MEDHX($app->dbh);
		$genData = $med_obj->getGenHealth($data);
		if(count($genData) > 0){
			if(count($genData['Patient']) > 0){
				$ptMedContidions = $genData['Patient'];
			}
			
			if(count($genData['Relatives']) > 0){
				$relMedContidions = $genData['Relatives'];
			}
		}
	}
	
	$response = array('Patient'=>$ptMedContidions, 'Relatives'=>$relMedContidions);
	
	return json_encode($response);
});

/*Return Medications Data*/
$this->get('/medications', function($request, $response, $service, $app) use(&$patientId){
	$medication_arr = array();
	$site_arr = array(1 => 'OS', 2 => 'OD', 3 => 'OU', 4 => 'PO');
	$medical_type_arr = array(1 => 'Systemic', 4 => 'Ocular', 5 => 'Systemic' , 6 => 'Ocular');
	
	$nkdaArr = $service->__get('nkdaArr');
	$where = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	/* Medications Data */
		$medication_qry = "
			SELECT
				id AS 'MedicationId',
				title as Name,
				type as MedicationType,
				med_route as Route,
				sites as Site,
				destination as Strength,
				DATE(begdate) as BeginDate,
				DATE(enddate) as EndDate,
				med_comments as Comments,
				ccda_code as RxNormCode,
				sig as Frequency,
				DATE(timestamp) as LastModifiedDate
			FROM 
				lists 
			WHERE 
				pid = ".$patientId." AND 
				type in (1,4) AND 
				allergy_status = 'Active'
				".$where."
		";
		$res = $app->dbh->imw_query($medication_qry);
		if($res && $app->dbh->imw_num_rows($res) > 0){
			while($row = $app->dbh->imw_fetch_assoc($res)){
				$row['BeginDate'] = ($row['BeginDate'] == '0000-00-00' || empty($row['BeginDate'])) ? '' : $row['BeginDate'];
				$row['EndDate'] = ($row['EndDate'] == '0000-00-00') ? '' : $row['EndDate'];
				if(empty($row['Site']) == false && isset($site_arr[$row['Site']])){
					$row['Site'] = $site_arr[$row['Site']];
				}else{
					unset($row['Site']);
				}
				$row['Name'] = filter_var($row['Name'],FILTER_SANITIZE_STRING);
				$row['MedicationType'] = $medical_type_arr[$row['MedicationType']];
				$row['Comments'] = filter_var($row['Comments'],FILTER_SANITIZE_STRING);
				$medication_arr[] = $row;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		if(!in_array('medication', $nkdaArr)) $medication_arr['NKDA'] = true;
		
		if(count($medication_arr) == 0) $medication_arr = 'No Medications';
		
		$response = array('Medications' => $medication_arr);
		return json_encode($response);
});

/*Return Surgeries Data*/
$this->get('/surgeries', function($request, $response, $service, $app) use(&$patientId){
	$surgeries_arr = array();
	$site_arr = array(1 => 'OS', 2 => 'OD', 3 => 'OU', 4 => 'PO');
	$medical_type_arr = array(1 => 'Systemic', 4 => 'Ocular', 5 => 'Systemic' , 6 => 'Ocular');
	
	$nkdaArr = $service->__get('nkdaArr');
	$where = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	/* Sx/Proc Data */
		$sx_proc_qry = "
			SELECT 
				id as ProcedureID,
				title as SurgeryName,
				sites as Site,
				type as ProcedureType,
				if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
					if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
						if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
							if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
								date_format(begdate,'%Y-%m-%d')
							)
						)
					)
				)
				as DateOfSurgery,
				referredby as ReferredBy,
				ccda_code as SNOMEDCT,
				comments as Comments,
				procedure_status as Status,
				DATE(timestamp) as LastModifiedDate
			FROM
				lists
			WHERE
				pid = ".$patientId." AND 
				type in (5,6) AND 
				allergy_status != 'Deleted' 
				".$where."
			ORDER BY 
				begdate ,id DESC
		";
		$res = $app->dbh->imw_query($sx_proc_qry);
		if($res && $app->dbh->imw_num_rows($res) > 0){
			while($row = $app->dbh->imw_fetch_assoc($res)){
				$row['DateOfSurgery'] = (empty($row['DateOfSurgery'])) ? '' : $row['DateOfSurgery'];
				$row['SurgeryName'] = filter_var($row['SurgeryName'],FILTER_SANITIZE_STRING);
				$row['ReferredBy'] = filter_var($row['ReferredBy'],FILTER_SANITIZE_STRING);
				if(empty($row['Site']) == false && $row['ProcedureType'] == 5){
					$row['Site'] = $site_arr[$row['Site']];
				}else{
					unset($row['Site']);
				}
				$row['ProcedureType'] = $medical_type_arr[$row['ProcedureType']];
				if(empty($row['Status']) || $row['Status'] === null || strtolower($row['Status']) == 'select') $row['Status'] = 'None';
				$surgeries_arr[] = $row;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		if(!in_array('surgery', $nkdaArr)) $surgeries_arr['NKDA'] = true;
		if(count($surgeries_arr) == 0) $surgeries_arr = 'No Surgeries';
		
		$response = array('Surgeries' => $surgeries_arr);
		return json_encode($response);
});

/* UDI [Unique Device Identifier] */
$this->get('/udi', function($request, $response, $service, $app) use(&$patientId){
	$tmp_array = array();
	$where = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	$udi_sql = "SELECT 
					title as DeviceNumber,
					assigning_authority_UDI as AssigningAuhtority,
					implant_status as Status,
					comments as Details,
					ccda_code as SNOWMEDCT,
					DATE(begdate) as ImplantDate
				FROM 
					lists
				WHERE 
					TYPE =9 AND LOWER( allergy_status ) != 'deleted' AND pid = ".$patientId." ".$where."";
					
	$res = $app->dbh->imw_query($udi_sql);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$row['Details'] = explode('||',$row['Details']);
			$row['Details'] = $row['Details'][0];
			$row['Status'] = ucfirst($row['Status']);
			$row['ImplantDate'] = ($row['ImplantDate'] !== null ) ? $row['ImplantDate'] : '';
			$tmp_array[] = $row;
		}				
	}	
	
	if(count($tmp_array) == 0) $tmp_array = 'No UDI Found';
	
	$response = array('UDI' => $tmp_array);
	return json_encode($response);	
});



/*Return Allergies Data*/
$this->get('/allergies', function($request, $response, $service, $app) use(&$patientId){
	$allergies_arr = array();
	$allergy_type_arr = array('fdbATDrugName' => 'Drug', 'fdbATIngredient' => 'Ingredient', 'fdbATAllergenGroup' => 'Allergen');
	
	$arrServ = $service->__get('arrServ');
	$nkdaArr = $service->__get('nkdaArr');
	
	$where = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	/* Allergies Data */
		$allergies_qry = "
			SELECT 
				id as AllergyID,
				title as Name,
				ccda_code as Code,
				severity as Severity,
				allergy_status as Status,
				begdate as OnSetDate,
				comments as Comments,
				ag_occular_drug as AllergyType,
				DATE(timestamp) as LastModifiedDate
			FROM 
				lists 
			WHERE 
				pid = ".$patientId." AND 
				type in (3,7) AND 
				allergy_status = 'Active'
				".$where."
		";
		$res = $app->dbh->imw_query($allergies_qry);
		if($res && $app->dbh->imw_num_rows($res) > 0){
			while($row = $app->dbh->imw_fetch_assoc($res)){
				$row['Name'] = filter_var($row['Name'],FILTER_SANITIZE_STRING);
				$row['Comments'] = filter_var($row['Comments'],FILTER_SANITIZE_STRING);
				if(empty($row['AllergyType']) == false){
					$row['AllergyType'] = $allergy_type_arr[$row['AllergyType']];
				}
				
				$arrKey = array_search(str_replace(' ','',strtolower($row['Severity'])), $arrServ);
				if(isset($row['Severity']) && empty($row['Severity']) == false) $row['Severity'] = $arrKey;
				$row['OnSetDate'] = ($row['OnSetDate'] !== null ) ? $row['OnSetDate'] : '';
				$allergies_arr[] = $row;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		if(!in_array('allergy', $nkdaArr)) $allergies_arr['NKDA'] = true;
		if(count($allergies_arr) == 0) $allergies_arr = 'No Allergies';
		
		$response = array('Allergies' => $allergies_arr);
		return json_encode($response);
});

/*Return Problem List Data*/
$this->get('/problemList', function($request, $response, $service, $app) use(&$patientId){
	$prob_list_arr = array();
	$where = str_ireplace('timestamp','DATE(onset_date)',$service->__get('whereCondition'));
	/* Problem List Data */
		$prob_qry = "
			SELECT 
				id as ProblemID,
				user_id as ProviderID,
				problem_name as ProblemName,
				prob_type as ProblemType,
				ccda_code as Code,
				status as Status,
				onset_date as OnsetDate,
				DATE(timestamp) as LastModifiedDate
			FROM 
				pt_problem_list
			WHERE 
				pt_id = ".$patientId." AND
				problem_name != '' AND
				LOWER(status) IN ('active','completed')
				".$where;
		
		$res = $app->dbh->imw_query($prob_qry);
		if($res && $app->dbh->imw_num_rows($res) > 0){
			while($row = $app->dbh->imw_fetch_assoc($res)){
				$row['ProblemName'] = filter_var($row['ProblemName'],FILTER_SANITIZE_STRING);
				$row['ProblemType'] = filter_var($row['ProblemType'],FILTER_SANITIZE_STRING);
				$row['Status'] = filter_var($row['Status'],FILTER_SANITIZE_STRING);
				$prob_list_arr[] = $row;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		if(count($prob_list_arr) == 0) $prob_list_arr = 'No Problems';
		
		$response = array('ProblemList' => $prob_list_arr);
		return json_encode($response);
});

/* Smoking Status [ Social History ] */
$this->get('/smokingStatus', function($request, $response, $service, $app) use(&$patientId){
	$returnArr = array();
	$ccda_obj = new IMW\CCDA($app->dbh, $patientId);
	$smokeArray = array();
	$sql = "SELECT 
				smoking_status as smokeStatus,
				DATE(modified_on) as smokeModifiedDate, 
				DATE(smoke_start_date) as smokeStartDate, 
				DATE(smoke_end_date) as smokeEndDate  
			FROM 
				social_history 
			WHERE 
				patient_id = '".$patientId."'";

	$res = $app->dbh->imw_query($sql);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			if(empty($row['smokeStatus']) == false){
				$arrTmp = explode('/',$row['smokeStatus']);
				$smoking_status = trim($arrTmp[1]);
			}
			
			$arrSmoking = array();
			$arrSmoking = $ccda_obj->smoking_status_srh(strtolower($smoking_status));
			
			$row['smokeStatus'] = ($arrSmoking['display_name'] && $arrSmoking['code']) ? $arrSmoking['display_name'].' (SNOMED-CT: '.$arrSmoking['code'].')' : '';
			$row['smokeModifiedDate'] = ($row['smokeModifiedDate'] != '0000-00-00' && empty($smoking_status) == false) ? $row['smokeModifiedDate'] : '';
			$row['smokeStartDate'] = ($row['smokeStartDate'] != '0000-00-00' && empty($smoking_status) == false) ? $row['smokeStartDate'] : '';
			$row['smokeEndDate'] = ($row['smokeEndDate'] != '0000-00-00' && empty($smoking_status) == false) ? $row['smokeEndDate'] : '';
			if(empty($row['smokeStatus']) == false){
				$smokeArray[] = $row;
			}
		}
	}
	if(count($smokeArray) == 0) $smokeArray = 'No Smoking History';
	$response = array('SmokeStatus' => $smokeArray);
	
	return json_encode($response);	
});

/* Lab Results & Values */
$this->get('/labResults', function($request, $response, $service, $app) use(&$patientId){
	$med_obj = new IMW\MEDHX($app->dbh);
	$where = $service->__get('whereCondition');
	$pt_lab_results = $med_obj->getPatientLabOrdered($patientId,$where);
	if($pt_lab_results === false) $pt_lab_results = 'No Lab Results';
	$response = array('LaboratoryTests' => $pt_lab_results);
	return json_encode($response);
});

/* Vital Signs */
$this->get('/vitalSigns', function($request, $response, $service, $app) use(&$patientId){
	$med_obj = new IMW\MEDHX($app->dbh);
	$tmp_arr = array();
	$where = $service->__get('whereCondition');
	$vs_arr = $med_obj->getVitalSigns($patientId,$where);
	
	$response = array('VitalSigns' => '');
	if(count($vs_arr) > 0){
		$tmp_arr = $vs_arr;	
	}
	if(count($tmp_arr) == 0) $tmp_arr = 'No Vital signs';
	$response = array('VitalSigns' => $tmp_arr);
	return json_encode($response);
});

/* Immunizations */
$this->get('/immunizations', function($request, $response, $service, $app) use(&$patientId){
	$tmp_arr =array();
	$nkdaArr = $service->__get('nkdaArr');
	
	$where = $service->__get('whereCondition');
	$where = str_ireplace('timestamp','DATE(administered_date)',$where);
	$qry_immu = "
				SELECT
					immunization_id as ImmunizationId,
					immunization_cvx_code as CvxCode,
					ndc_code as NDCCode,
					DATE(administered_date) as Date,
					scpStatus as Status,
					lot_number as LotNumber,
					manufacturer as Manufacturer,
					note as AdminNotes,
					refusal_reason as RefusalReason	
				FROM 
					immunizations 
				WHERE 
					patient_id = '".$patientId."'
					".$where."
					";
	$res = $app->dbh->imw_query($qry_immu);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row_immu = $app->dbh->imw_fetch_assoc($res)){
			$temp_immunization_id = explode(' - ',$row_immu['ImmunizationId']);
			$row_immu['ImmunizationId'] = $temp_immunization_id[1];
			$row_immu['VaccineName'] = htmlentities($row_immu['ImmunizationId']);
			unset($row_immu['ImmunizationId']);
			$row_immu['Date'] = (empty($row_immu['Date']) == false && $row_immu['Date'] != '0000-00-00') ? $row_immu['Date'] : '';
			$tmp_arr[] = $row_immu;
		}
	}
	if(!in_array('immunizations', $nkdaArr)) $tmp_arr['NKDA'] = true;
	if(count($tmp_arr) == 0) $tmp_arr = 'No Immunizations';
	$response = array('Immunizations' => $tmp_arr);
	return json_encode($response);
});

/* Assessment & Plan of Treatment */
$this->get('/assessmentPlan', function($request, $response, $service, $app) use(&$patientId){
	$med_obj = new IMW\MEDHX($app->dbh,1);
	$where = $service->__get('whereCondition');
	
	//XML Sections
	$arrTND = array("assessment", "plan", "ne", "resolve","eye","conmed","pbid");
	$arrTNU = array("dt_time", "usrId");
	$assess_arr = $med_obj->valuesNewRecordsAssess($where,$patientId,'*');
	
	$return_data = array();
	foreach($assess_arr as $assess_array){
		$xml = new DOMDocument;
		$xml->loadXML( $assess_array['assess_plan'] );
		
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
		
		$arrApVals = $tmp_arr = $tmp_arr2 = array();
		if(count($arrRet) > 0){
			$arrApVals = $arrRet['data']['ap'];
		}
		
		foreach($arrApVals as $obj){
			if($obj['assessment'] != ""){
				$split_by_colon = explode(';',$obj['assessment']);
				if(isset($split_by_colon[1])){
					$temp_assess_text_part = explode('(',trim($split_by_colon[1]));
				}else{
					$temp_assess_text_part = explode('(',trim($split_by_colon[0]));	
				}
				$assess_text_part	= htmlentities($temp_assess_text_part[0]);
				
				if(empty($assess_text_part) == false){
					//$tmp_arr2['Assessment'] = trim($assess_text_part);
					$return_data[$assess_array['dos']]['Assessment'][] = trim($assess_text_part);
				}
				
				if(empty($obj['plan']) == false){
					//$tmp_arr2['Plans'] = trim(htmlentities($obj['plan']));
					$return_data[$assess_array['dos']]['Plan'][] = trim(htmlentities($obj['plan']));
				}
				$tmp_arr[] = $tmp_arr2;
				unset($tmp_arr2);
			}
		}
		//$return_data[$assess_array['dos']] = $tmp_arr;
	}
	if(count($return_data) == 0) $return_data = 'No Assessment Plans';
	$response = array('AssessmentPlan' => $return_data);
	return json_encode($response);
});

/* Patient Goals */
$this->get('/patientGoals', function($request, $response, $service, $app) use(&$patientId){
	$where = $service->__get('whereCondition');
	$where = str_ireplace('timestamp','DATE(`goal_date`)',$where);
	
	$goal_qry = 'SELECT `id`, `patient_id`, `form_id`, `goal_set`, `loinc_code`, `goal_data`, `goal_data_type`, `gloal_data_type_unit`, `operator_id`,  DATE(`goal_date`) AS \'goal_date\',DATE(`goal_date`) AS \'goal_date_show\' from patient_goals where  patient_id="'.$patientId.'" AND delete_status = 0 '.$where.'';
	
	$tmp_arr = $returnArr = array();
	$res = $app->dbh->imw_query($goal_qry);
	if($res && $app->dbh->imw_num_rows($res)>0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$tmp_arr['Goal'] = trim(addslashes($row["goal_set"]));
			$tmp_arr['Value'] = trim(addslashes($row["goal_data"]));
			$tmp_arr['Date'] = trim(addslashes($row["goal_date"]));
			$returnArr[] = $tmp_arr;
			unset($tmp_arr);
		}
	}
	if(count($returnArr) == 0) $returnArr = 'No Patient Goals';
	$response = array('PatientGoals' => $returnArr);
	return json_encode($response);
});

/* Health & Concerns */
$this->get('/healthConcerns', function($request, $response, $service, $app) use(&$patientId){
	$resp_observation = $resp_concern = $resp_rel_observation = array();
	$where = $service->__get('whereCondition');
	$where = str_ireplace('timestamp','DATE(hco.observation_date)',$where);
	$sql = "
		SELECT
			hco.id as ID,
			hco.snomed_code as Code,
			hco.observation_date as ObservationDate,
			hco.observation as Observation,
			DATE(hco.observation_date) as ObservationDate,
			
			hcc.id as ConcernID,
			hcc.concern as Concern,
			DATE(hcc.concern_date) as ConcernDate,
			hcc.status as ConcernStatus,
			
			hcr.id as RelConcernID,
			hcr.snomed_code as RelCode,
			hcr.rel_observation as RelObservation,
			DATE(hcr.rel_observation_date) as RelObservationDate
		FROM 
			hc_observations hco
			LEFT JOIN hc_concerns hcc ON( hco.id = hcc.observation_id AND hcc.del_status = 0)
			LEFT JOIN hc_rel_observations hcr ON ( hco.id = hcr.observation_id AND hcr.del_status = 0)
		WHERE
			hco.del_status = 0 AND
			hco.pt_id = ".$patientId."
			".$where."
	";
	
	$res = $app->dbh->imw_query($sql);
	$response = $tmp_arr = array();
	if($res && $app->dbh->imw_num_rows($res) > 0){
		$previous_obser = '';
		while($row = $app->dbh->imw_fetch_assoc($res)){
			
			$response[$row['ID']]['ObservationId'] = $row['ID'];
			$response[$row['ID']]['Name'] = $row['Observation'];
			$response[$row['ID']]['Code'] = $row['Code'];
			$response[$row['ID']]['Date'] = $row['ObservationDate'];
			
			/*Concerns*/
			if( empty($row['Concern']) == false )
			{
				if( isset($response[$row['ID']]['Concerns']) === false	 )
					$response[$row['ID']]['Concerns'] = array();
				
				$concern = &$response[$row['ID']]['Concerns'][$row['ConcernID']];
				
				$concern['Id'] = $row['ConcernID'];
				$concern['Name'] = $row['Concern'];
				$concern['Status'] = ucfirst($row['ConcernStatus']);
				$concern['Date'] = (empty($row['ConcernDate']) == false && $row['ConcernDate'] != '0000-00-00') ? $row['ConcernDate'] : '';
			}
			
			/*Relative Concerns*/
			if( empty($row['RelObservation']) == false )
			{
				if( isset($response[$row['ID']]['RelConcerns']) === false	 )
					$response[$row['ID']]['RelConcerns'] = array();
				
				$Relconcern = &$response[$row['ID']]['RelConcerns'][$row['RelConcernID']];
				
				$Relconcern['Id'] = $row['RelConcernID'];
				$Relconcern['Name'] = $row['RelObservation'];
				$Relconcern['Code'] = $row['RelCode'];
				$Relconcern['Date'] = (empty($row['RelObservationDate']) == false && $row['RelObservationDate'] != '0000-00-00') ? $row['RelObservationDate'] : '';
			}
		}
	}
	
	/* Reset Keys */
	$response = array_values($response);
	$resetKeys = array('Concerns','RelConcerns');
	
	foreach($response as &$resp)
	{
		foreach($resetKeys as $key)
		{
			if( isset($resp[$key]) )
			{
				$resp[$key] = array_values($resp[$key]);
			}
		}
	}
	
	return json_encode($response);
});

//Patient Complaint History
$this->get('/complaintHistory', function($request, $response, $service, $app) use(&$patientId){
	$where = str_ireplace('timestamp','date_of_service',$where);
	$returnData = array();
	
	//Get CC History
	$chkQry = $app->dbh->imw_query('
		Select 
			cc_id as ID,
			date_of_service as DateOfService,
			reason as History,
			ccompliant as CC
		FROM 
			chart_left_cc_history 
		WHERE 
			patient_id  = '.$patientId.'
			'.$where.'
		ORDER BY date_of_service DESC
	');
	
	if($app->dbh->imw_num_rows($chkQry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkQry)){
			$returnData[$row['DateOfService']][] = $row;
		}	
	}
	
	if(count($returnData) == 0) $returnData = 'No History found';
	
	$response = array('ComplaintHistory' => $returnData);
	
	return json_encode($response);
});

/*Update Patient Allergies iPortal*/
$this->post('/allergies', function($request, $response, $service, $app) {
    $request->__set('newAdd', 0);
	if($request->__isset('PtAllergyId') && trim($request->__get('PtAllergyId')) != ''){
		$service->validateParam('PtAllergyId', 'Please provide a valid Patient Allergy ID.')->isInt()->notNull()->isPtAllergy($this);
	}else{
		$request->__set('newAdd', 1);
		$request->__set('Type', 7);
		$request->__set('DelStatus', 0);
		$request->__set('AllergyStatus', 'Active');
		$request->__set('Date', date('Y-m-d H:i:s'));
	}
	
	if($request->__isset('AllergyId') && trim($request->__get('AllergyId') != '')){
		$service->validateParam('AllergyId', 'Please provide a valid allergy id.')->notNull()->isAllergy($this);
	}
	
	if($request->__isset('OnSetDate') && trim($request->__get('OnSetDate') != '')){
		$service->validateParam('OnSetDate', 'Please provide valid Onset Date.')->isDate();		
	}
	
	if($request->__isset('Severity') && trim($request->__get('Severity') != '')){
		$arrServ = $service->__get('arrServ');
		
		$severity = trim($request->__get('Severity'));
		
		if(isset($arrServ[$severity]) && empty($arrServ[$severity]) == false){}
		else $request->__set('Severity', '');
		
		$service->validateParam('Severity', 'Please provide valid Severity.')->notNull()->isInt();		
	}
	
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->allergy;
		
		if($request->__isset('newAdd') && trim($request->__get('newAdd')) == 1) $this->addField( $parameters );
		else $this->saveField( $parameters );
    }
    catch (Exception $e)
    {
	$response->append($e->getMessage());
	$this->abort(503);
    }
    
	return json_encode('Request Saved. Data is pending for approval.');
});


/*Update Patient Medications iPortal*/
$this->post('/medications', function($request, $response, $service, $app) {
    $request->__set('newAdd', 0);
	if($request->__isset('PtMedicationId') && trim($request->__get('PtMedicationId')) != ''){
		$service->validateParam('PtMedicationId', 'Please provide a valid Patient Medication ID.')->isInt()->notNull()->isPtMedication($this);
	}else{
		$request->__set('newAdd', 1);
		$request->__set('DelStatus', 0);
		$request->__set('AllergyStatus', 'Active');
		$request->__set('Date', date('Y-m-d H:i:s'));
	}
	
	$service->validateParam('MedicationId', 'Please provide a valid Medication id.')->isInt()->notNull()->isMedication($this);
	
	if($request->__isset('BeginDate') && trim($request->__get('BeginDate') != '')){
		$service->validateParam('BeginDate', 'Please provide valid Begin Date.')->isDate();		
	}
	
	if($request->__isset('EndDate') && trim($request->__get('EndDate') != '')){
		$service->validateParam('EndDate', 'Please provide valid End Date.')->isDate();		
	}
	
	$request->__set('Type', 1);
	if($request->__isset('Site') && trim($request->__get('Site') != '')){
		$service->validateParam('Site', 'Please provide valid site value.')->isInt();
		$request->__set('Type', 4);
	}
	
	if($request->__isset('Route') && trim($request->__get('Route')) != ''){
		$service->validateParam('Route', 'Please provide valid Route ID.')->isInt()->notNull()->isMedRoute($this);
	}
	
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->medications;
		
		if($request->__isset('newAdd') && trim($request->__get('newAdd')) == 1) $this->addField( $parameters );
		else $this->saveField( $parameters );
		//$this->saveField($parameters);
    }
    catch (Exception $e)
    {
		$response->append($e->getMessage());
		$this->abort(503);
    }
    
	return json_encode('Request Saved. Data is pending for approval.');
});

/*Update Patient Surgeries iPortal*/
$this->post('/surgeries', function($request, $response, $service, $app) {
    
	$service->validateParam('PtSurgeryId', 'Please provide a valid Patient Surgery ID.')->isInt()->notNull()->isPtSurgery($this);
	
	$service->validateParam('SurgeryId', 'Please provide a valid allergy id.')->isInt()->notNull()->isSxProcedure($this);
	
	if($request->__isset('RefPhysicianId') && trim($request->__get('RefPhysicianId') != '')){
		$service->validateParam('RefPhysicianId', 'Please provide a valid referring physician id.')->isInt()->isRefPhysician($this);
	}
	
	if($request->__isset('ProcedureDate') && trim($request->__get('ProcedureDate') != '')){
		$service->validateParam('ProcedureDate', 'Please provide valid Begin Date.')->isDate();		
	}
	
	if($request->__isset('Site') && trim($request->__get('Site') != '')){
		$service->validateParam('Site', 'Please provide valid site value.')->isInt();		
	}
	
	try
    {
	/*Parameters to be saved for this API Call*/
	$parameters = $app->saveParameters->surgeries;
	$this->saveField($parameters);
    }
    catch (Exception $e)
    {
	$response->append($e->getMessage());
	$this->abort(503);
    }
    
	return json_encode('Request Saved. Data is pending for approval.');
});

	
$this->respond(function($request, $response, $service) use(&$patientId) {

});