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
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	if($request->__isset('startDate') && trim($request->__get('startDate')) !== '' ){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
		$service->__set('startDate', $startDate);
		
		/*if($request->__isset('endDate') == false){
			
			$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
			
		}*/
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
});

/*Return Ocular Data*/
$this->respond(array('POST','GET'), '/ocular', function($request, $response, $service,$app) use(&$patientId){
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
				DATE_FORMAT(timestamp, '%m-%d-%Y') as ModifiedDate
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
		
		$response = array('Ocular' => $ocular_arr);
		return json_encode($response);
});

/*Return General Health Data*/
$this->respond(array('POST','GET'), '/generalHealth', function($request, $response, $service, $app) use(&$patientId)  {
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
				`desc_cancer`
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
$this->respond(array('POST','GET'), '/medications', function($request, $response, $service, $app) use(&$patientId){
	$medication_arr = array('Ocular'=>array(),'Systemic'=>array());
	$site_arr = array(1 => 'Left', 2 => 'Right', 3 => 'Both', 4 => 'Oral');
	$medical_type_arr = array(1 => 'Systemic', 4 => 'Ocular');
	$where = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	/* Medications Data */
		$medication_qry = "
			SELECT
				id AS 'MedicationId',
				title as Name,
				type as MedicationType,
				med_route as Route,
				destination as Strength,
				DATE_FORMAT(begdate, '%m-%d-%Y') as BeginDate,
				DATE_FORMAT(enddate, '%m-%d-%Y') as EndDate,
				comments as Comments,
				ccda_code as RxNormCode,
				sig as Direction,
				sites as Site,
				DATE_FORMAT(timestamp, '%m-%d-%Y') as LastModifiedDate
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
				$row['BeginDate'] = ($row['BeginDate'] == '00-00-0000' || empty($row['BeginDate'])) ? '' : $row['BeginDate'];
				$row['EndDate'] = ($row['EndDate'] == '00-00-0000') ? '' : $row['EndDate'];
				if(!empty($row['Site']) && $row['MedicationType'] == 4){
					$row['Site'] = $site_arr[$row['Site']];
				}
				else{
					unset($row['Site']);
				}
				$row['Name'] = filter_var($row['Name'],FILTER_SANITIZE_STRING);
				$row['MedicationType'] = $medical_type_arr[$row['MedicationType']];
				$row['Comments'] = filter_var($row['Comments'],FILTER_SANITIZE_STRING);
				$medication_arr[$row['MedicationType']][] = $row;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		//if(count($medication_arr) == 0) $medication_arr = 'No Medications';
		
		$response = $medication_arr;
		$response = json_encode($response);
		// Data Formatted According to IOS Developer //
		$response = (str_ireplace('\n','',$response));
		$response = (preg_replace('/\s+/',' ', $response));
		return ($response);
});

/*Return Surgeries Data*/
$this->respond(array('POST','GET'), '/surgeries', function($request, $response, $service, $app) use(&$patientId){
	$response = array();
	$surgeries_arr = array('Eye_Surgery'=>array(),'Other_Surgery'=>array(),'UDI'=>array());
	$site_arr = array(1 => 'LEFT', 2 => 'RIGHT', 3 => 'BOTH', 4 => 'ORAL');
	$medical_type_arr = array( 5 => 'Other_Surgery' , 6 => 'Eye_Surgery' , 9 => 'UDI');
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
								date_format(begdate,'%m-%d-%Y')
							)
						)
					)
				)
				as DateOfSurgery,
				referredby as ReferredBy,
				ccda_code as SNOMEDCT,
				comments as Comments,
				procedure_status as Status,
				DATE_FORMAT(timestamp, '%m-%d-%Y') as LastModifiedDate,
				assigning_authority_UDI as Assigning_Auhtority,
				implant_status as Implant_Status
				
			FROM
				lists
			WHERE
				pid = ".$patientId." AND 
				type in (5,6,9) AND 
				allergy_status != 'Deleted' 
				".$where."
			ORDER BY 
				id ASC
		";
		$res = $app->dbh->imw_query($sx_proc_qry);
		if($res && $app->dbh->imw_num_rows($res) > 0){
			while($row = $app->dbh->imw_fetch_assoc($res)){
				if($row['ProcedureType']!=9){
					$row['DateOfSurgery'] = (empty($row['DateOfSurgery'])) ? '' : $row['DateOfSurgery'];
					$row['SurgeryName'] = filter_var($row['SurgeryName'],FILTER_SANITIZE_STRING);
					$row['ReferredBy'] = filter_var($row['ReferredBy'],FILTER_SANITIZE_STRING);
					if(empty($row['Site']) == false && $row['ProcedureType'] == 6){
						$row['Site'] = $site_arr[$row['Site']];
					}
					else{
						unset($row['Site']);
					}
					$row['ProcedureType'] = $medical_type_arr[$row['ProcedureType']];
					
					$surgeries_arr[$row['ProcedureType']][] = $row;
				}
				else{
					$udi['ProcedureID'] = $row['ProcedureID'];
					$DateOfImplant = (empty($row['DateOfSurgery'])) ? '' : $row['DateOfSurgery'];
					if($DateOfImplant!=""){
						$udi['DateOfImplant'] = date('m-d-Y',strtotime($DateOfImplant));
					}
					else{
						$udi['DateOfImplant'] = '';
					}
					$udi['UdiId'] = filter_var($row['SurgeryName'],FILTER_SANITIZE_STRING);
					$udi['Status'] = $row['Implant_Status'];
					$surgeries_arr["UDI"][] = $udi;
				}
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		$response =  $surgeries_arr;
		//$response['UDI'] = $tmp_array;
		
		return json_encode($response);
});

/* Implant Device Detail */
$this->respond(array('POST','GET'), '/ImplantDeviceDetail', function($request, $response, $service, $app) use(&$patientId){
	
	$device_detail = array();
	$parse_udi = array();
	
	$id = $request->__get("id");
	$query = "select comments from lists where id=$id ";
	$sql = imw_query($query);
	$cmnt = imw_fetch_assoc($sql);
	$comments = explode('||', $cmnt['comments']);
	//$parse_udi = 
	$jsonData1 = stripslashes(html_entity_decode($comments[1]));
	$jsonData1 = str_replace("\\", "",$jsonData1);
	$parse_udi = json_decode($jsonData1,true);
	//var_dump($parse_udi);
	$jsonData2 = stripslashes(html_entity_decode($comments[2]));
	$jsonData2 = str_replace("\\", "",$jsonData2);
	$device_detail = json_decode($jsonData2,true);
		
	$Detail['DeviceUDI'] = $parse_udi['udi'];
	$Detail['DeviceID'] = $parse_udi['di']; 
	$Detail['SerialNumber'] = $parse_udi['serial_number'];
	$Detail['LotNumber'] = $parse_udi['lot_number'];
	$Detail['ManufacturingDate'] = $parse_udi['manufacturing_date_original'];
	$Detail['ExpirationDate'] = $parse_udi['expiration_date_original'];
	
	
	$Detail['BrandName'] = $device_detail['brandName'];
	$Detail['ModelNumber'] = $device_detail['versionModelNumber'];
	$Detail['MRISafetyStatus'] = $device_detail['MRISafetyStatus'];
	$Detail['CompanyName'] = $device_detail['companyName'];
	$LabeledContainsNRL = $device_detail['labeledContainsNRL'];
	if($LabeledContainsNRL=='true'){
		$Detail['LabeledContainsNRL'] = true;
	}
	else if($LabeledContainsNRL=='false'){
		$Detail['LabeledContainsNRL'] = false;
	}
	$LabeledContainsNoNRL = $device_detail['labeledNoNRL'];
	if($LabeledContainsNoNRL=='true'){
		$Detail['LabeledContainsNoNRL'] = true;
	}
	else if($LabeledContainsNoNRL=='false'){
		$Detail['LabeledContainsNoNRL'] = false;
	}
	$device_detail['gmdnTerms'][1]['gmdnPTName'] = 'aqeqwewqedfgdfg';
	$device_detail['gmdnTerms'][1]['gmdnPTDefinition'] = 'aqeqwewqetrytrytrytrytdfgdfg';
	$device_detail['gmdnTerms'][2]['gmdnPTName'] = 'aqeqweghghkhjkwqedfgdfg';
	
	$name .= '<style>div { background-color: #FDF6E5; color:#012879; }  </style><div style="width:100%; background-color: #FDF6E5;"><div style="float:left;width:100%;">&nbsp;&nbsp;&nbsp;<label style="display: inline-block; margin-top: 10px;">Gmdn PT Name</label><ol style="margin-left:3px;padding-left:15px;font-size:15px; line-height:1.1;">';
	
	foreach($device_detail['gmdnTerms'] as $value) {
	
		$name .= '<li style=" margin-left:10px;"><p style="text-align:justify;width:90%; color:#505050;">'.$value['gmdnPTName'].'</p></li>';
	}
	$name .='</ol><hr style="background-color:white;height:5px; border:0px;">';
	$defination .='&nbsp;&nbsp;&nbsp;Gmdn PT Definition<ol style="margin-left:3px;padding-left:15px;font-size:15px; line-height:1.1;">';
	foreach($device_detail['gmdnTerms'] as $value) {
	
		$defination .= '<li style=" margin-left:10px;"><p style="text-align:justify; width:90%; color:#505050;">'.$value['gmdnPTDefinition'].'</p></li>';
	}
	$defination .= '</ol></div></div>';
	
	$Detail['name'] = htmlspecialchars_decode($name);
	$Detail['name'] = stripslashes(html_entity_decode($Detail['name'],true));
	
	$Detail['defination'] = htmlspecialchars_decode($defination);
	$Detail['defination'] = stripslashes(html_entity_decode($Detail['defination'],true));
	
	$response = array('DeviceDetail' => $Detail);
	return json_encode($response);
	
});

/* UDI [Unique Device Identifier] */
$this->respond(array('POST','GET'), '/udi', function($request, $response, $service, $app) use(&$patientId){
	$tmp_array = array();
	$where_udi = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	$udi_sql = "SELECT 
					title as DeviceNumber,
					assigning_authority_UDI as AssigningAuhtority,
					implant_status as Status,
					comments as Details,
					ccda_code as SNOWMEDCT,
					DATE_FORMAT(begdate, '%m-%d-%Y') as ImplantDate
				FROM
					lists
				WHERE 
					TYPE =9 AND LOWER( allergy_status ) != 'deleted' AND pid = ".$patientId." ".$where_udi."";
					
	$resUDI = $app->dbh->imw_query($udi_sql);
	if($resUDI && $app->dbh->imw_num_rows($resUDI) > 0){
		while($rowUDI = $app->dbh->imw_fetch_assoc($resUDI)){
			$rowUDI['Details'] = explode('||',$rowUDI['Details']);
			$rowUDI['Details'] = $rowUDI['Details'][0];
			$rowUDI['Status'] = ucfirst($rowUDI['Status']);
			$rowUDI['ImplantDate'] = ($rowUDI['ImplantDate'] !== null ) ? $rowUDI['ImplantDate'] : '';
			$tmp_array[] = $rowUDI;
		}				
	}
	
	//if(count($tmp_array) == 0) $tmp_array = 'No UDI Found';
	
	$response = array('UDI' => $tmp_array);
	return json_encode($response);	
});



/*Return Allergies Data*/
$this->respond(array('POST','GET'), '/allergies', function($request, $response, $service, $app) use(&$patientId){
	$allergies_arr = array();
	$allergy_type_arr = array('fdbATDrugName' => 'Medical', 'fdbATIngredient' => 'Non-Medical', 'fdbATAllergenGroup' => 'Non-Medical');
	$where = str_ireplace('timestamp','DATE(begdate)',$service->__get('whereCondition'));
	/* Allergies Data */
		$allergies_qry = "
			SELECT 
				id as AllergyID,
				title as Name,
				ccda_code as Code,
				severity as Severity,
				allergy_status as Status,
				DATE_FORMAT(begdate, '%m-%d-%Y') as OnSetDate,
				comments as Comments,
				ag_occular_drug as AllergyType,
				DATE_FORMAT(timestamp, '%m-%d-%Y') as LastModifiedDate
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
				$allergies_arr[] = $row;
			}
			$app->dbh->imw_free_result($res);
			unset($res);
		}
		
		//if(count($allergies_arr) == 0) $allergies_arr = 'No Allergies';
		
		$response = array('Allergies' => $allergies_arr);
		return json_encode($response);
});

/*Return Problem List Data*/
$this->respond(array('POST','GET'), '/problemList', function($request, $response, $service, $app) use(&$patientId){
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
				DATE_FORMAT(onset_date, '%m-%d-%Y') as OnsetDate,
				DATE_FORMAT(timestamp, '%m-%d-%Y') as LastModifiedDate
			FROM 
				pt_problem_list
			WHERE 
				pt_id = ".$patientId." AND
				problem_name != '' AND
				LOWER(status) IN ('active','completed') AND
				form_id = 0
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
		
		//if(count($prob_list_arr) == 0) $prob_list_arr = 'No Problems';
		
		$response = array('ProblemList' => $prob_list_arr);
		return json_encode($response);
});

/* Smoking Status [ Social History ] */
$this->respond(array('POST','GET'), '/smokingStatus', function($request, $response, $service, $app) use(&$patientId){
	$returnArr = array();
	$ccda_obj = new IMW\CCDA($app->dbh, $patientId);
	$smokeArray = array();
	$sql = "SELECT 
				smoking_status as smokeStatus,
				DATE_FORMAT(modified_on, '%m-%d-%Y') as smokeModifiedDate,
				DATE_FORMAT(smoke_start_date, '%m-%d-%Y') as smokeStartDate,
				DATE_FORMAT(smoke_end_date, '%m-%d-%Y') as smokeEndDate
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
			$row['smokeModifiedDate'] = ($row['smokeModifiedDate'] != '00-00-0000' && empty($smoking_status) == false) ? $row['smokeModifiedDate'] : '';
			$row['smokeStartDate'] = ($row['smokeStartDate'] != '00-00-0000' && empty($smoking_status) == false) ? $row['smokeStartDate'] : '';
			$row['smokeEndDate'] = ($row['smokeEndDate'] != '00-00-0000' && empty($smoking_status) == false) ? $row['smokeEndDate'] : '';
			if(empty($row['smokeStatus']) == false){
				$smokeArray[] = $row;
			}
		}
	}
	//if(count($smokeArray) == 0) $smokeArray = 'No Smoking History';
	$response = array('SmokeStatus' => $smokeArray);
	
	return json_encode($response);	
});


/* Clinical Dates */
$this->respond(array('POST','GET'), '/clinicalDates', function($request, $response, $service, $app) use(&$patientId){	
	$dates['dates'] = array();	
	$sql = "SELECT c1.id as form_id, date_format(c1.date_of_service,'%m-%d-%Y') as date_of_service, c1.create_dt
		FROM chart_master_table c1 ".
		"WHERE c1.patient_id = '".$patientId."' ".
		"and c1.finalize=1 ".
		"ORDER BY c1.date_of_service DESC, c1.id DESC ";
	$res = $app->dbh->imw_query($sql);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		$i=0;
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$dates['dates'][$i]['date'] = $row['date_of_service'];
			$dates['dates'][$i]['form_id'] = $row['form_id'];
			$dates['dates'][$i]['create_dt'] = $row['create_dt'];
			$i++;
		}
	}
	return json_encode($dates);
});


/* Lab Reports & Values */
$this->respond(array('POST','GET'), '/labReports', function($request, $response, $service, $app) use(&$patientId){	
	$LaboratoryReports['REPORT'] = array();
	
	
	// Get lab test data Id
	$query = "SELECT 
				lab_test_data_id as LabId,
				lab_order_date,lab_order_time,lab_test_order_by
			FROM 
				lab_test_data
			WHERE 
				lab_patient_id = '".$patientId."'
				AND lab_status IN(1,2)"; 
	$report = $app->dbh->imw_query($query);
	
	if($report && $app->dbh->imw_num_rows($report) > 0){
		$i=0;
		while($result = $app->dbh->imw_fetch_assoc($report)){
		
			$LaboratoryReports['REPORT'][$i]["Order"] = $result['LabId'];
			$LaboratoryReports['REPORT'][$i]["Order_Date"] = date('m-d-Y',strtotime($result['lab_order_date']));
			$LaboratoryReports['REPORT'][$i]["Order_Time"] = $result['lab_order_time'];
			
			// get physician name
			$qry = "select lname,fname from users 
					where id = '".$result['lab_test_order_by']."' AND locked = 0";
			
			$users = $app->dbh->imw_query($qry);
			$res = $app->dbh->imw_fetch_assoc($users);
			if($res['lname']!="" && $res['fname']!=""){
				$LaboratoryReports['REPORT'][$i]["Order_By"] = $res['lname'].', '.$res['fname'];
			}
			else{
				$LaboratoryReports['REPORT'][$i]["Order_By"] = "";
			}
			
			// get result from lab_observation_requested
			
			$qry = "select service,loinc from lab_observation_requested 
					where lab_test_id = '".$result['LabId']."' AND del_status = 0";
			
			$lab_obs_req = $app->dbh->imw_query($qry);
			while($res_loreq = $app->dbh->imw_fetch_assoc($lab_obs_req)){
				$service_arr[] = ucfirst($res_loreq['service']);
			}
			$service_arr = array_filter($service_arr);
			$LaboratoryReports['REPORT'][$i]["service_names"] = implode(', ',$service_arr);
			unset($service_arr);
			
			// get result from lab_observation_result
			
			//$LaboratoryReports['REPORT'][$i]["Result"] = "";
			$qry = "select observation,result_loinc,result from lab_observation_result 
					where lab_test_id = '".$result['LabId']."' AND del_status = 0";
			$lab_obs_res = $app->dbh->imw_query($qry);
			while($res_lores = $app->dbh->imw_fetch_assoc($lab_obs_res)){
				if($res_lores['result']!=""){
					$result_arr[] = ucfirst($res_lores['observation'].':'.$res_lores['result']);
				}
			}
			$result_arr = array_filter($result_arr);
			$LaboratoryReports['REPORT'][$i]["Result"]=implode(', ',$result_arr);
			unset($result_arr);
				
			// get result from lab_specimen
			
			//$LaboratoryReports['REPORT'][$i]["Specimen"] = "";
			$qry = "select collection_type from lab_specimen 
					where lab_test_id = '".$result['LabId']."'";
			$lab_spe = $app->dbh->imw_query($qry);
			while($res_spe = $app->dbh->imw_fetch_assoc($lab_spe)){
				$arr[] = ucfirst($res_spe['collection_type']);
			}
			$arr = array_filter($arr);
			$LaboratoryReports['REPORT'][$i]["Specimen"] = implode(', ',$arr);
			
			unset($arr);
			$i++;
		}
	}
	return json_encode($LaboratoryReports);
});

/* Lab Details & Values */
$this->respond(array('POST','GET'), '/labDetails', function($request, $response, $service, $app) use(&$patientId){
	$LabReports = array();
	$order_id = $request->__get('order_id');
	
	// get result of lab_observation_requested
	$qry = "select * from lab_observation_requested 
					where lab_test_id = '".$order_id."' AND del_status = 0";
	$lab_obs_req = $app->dbh->imw_query($qry);
	
	if($lab_obs_req && $app->dbh->imw_num_rows($lab_obs_req) > 0){
		$i=0;
		while($res = $app->dbh->imw_fetch_assoc($lab_obs_req))
		{
			$LabReports['observation'][$i]['service'] = $res['service'];
			$LabReports['observation'][$i]['loinc'] = $res['loinc'];
			
			if($res['start_date']!='0000-00-00'){
				$start_date = date('m-d-Y',strtotime($res['start_date']));
			}
			else{
				$start_date = '00-00-0000';
			}
			
			$LabReports['observation'][$i]['start_date'] = $start_date;
			$LabReports['observation'][$i]['start_time'] = $res['start_time'];
			
			if($res['end_date']!='0000-00-00'){
				$end_date = date('m-d-Y',strtotime($res['end_date']));
			}
			else{
				$end_date = '00-00-0000';
			}
			
			$LabReports['observation'][$i]['end_date'] = $end_date;
			$LabReports['observation'][$i]['end_time'] = $res['end_time'];
			$LabReports['observation'][$i]['clinical_info'] = $res['clinical_info'];
			$LabReports['observation'][$i]['url'] = "http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=".$res['loinc']."&mainSearchCriteria.v.cs=2.16.840.1.113883.6.1&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en";
			$i++;
		}
	}
	else{
		$LabReports['observation'] = array();
	}
	
	// get result of SPECIMEN
	$qry = "select * from lab_specimen 
					where lab_test_id = '".$order_id."'";
	$lab_spe = $app->dbh->imw_query($qry);
	
	if($lab_spe && $app->dbh->imw_num_rows($lab_spe) > 0){
		$i=0;
		while($res = $app->dbh->imw_fetch_assoc($lab_spe))
		{
			$LabReports['specimen'][$i]['type'] = $res['collection_type'];
			
			if($res['collection_start_date']!='0000-00-00'){
				$start_date = date('m-d-Y',strtotime($res['collection_start_date']));
			}
			else{
				$start_date = '00-00-0000';
			}
			$LabReports['specimen'][$i]['start_date'] = $start_date;
			$LabReports['specimen'][$i]['start_time'] = $res['collection_start_time'];
			if($res['collection_start_date']!='0000-00-00'){
				$end_date = date('m-d-Y',strtotime($res['end_date']));
			}
			else{
				$end_date = '00-00-0000';
			}
			$LabReports['specimen'][$i]['end_date'] = $end_date;
			$LabReports['specimen'][$i]['end_time'] = $res['collection_end_time'];
			$LabReports['specimen'][$i]['condition'] = $res['collection_condition'];
			$LabReports['specimen'][$i]['rejection'] = $res['collection_rejection'];
			$LabReports['specimen'][$i]['comment'] = $res['collection_comments'];
			$i++;
		}
	}
	else{
		$LabReports['specimen'] = array();
	}
		
	// get result of lab_observation_result
	$qry = "select * from lab_observation_result 
					where lab_test_id = '".$order_id."' AND del_status = 0";
	$lab_obs_res = $app->dbh->imw_query($qry);
	
	if($lab_obs_res && $app->dbh->imw_num_rows($lab_obs_res) > 0){
		$i=0;
		while($res = $app->dbh->imw_fetch_assoc($lab_obs_res))
		{
			$LabReports['result'][$i]['observation'] = $res['observation'];
			$LabReports['result'][$i]['loinc'] = $res['result_loinc'];
			$LabReports['result'][$i]['result'] = $res['result'];
			$LabReports['result'][$i]['uom'] = $res['uom'];
			$LabReports['result'][$i]['result_range'] = $res['result_range'];
			$LabReports['result'][$i]['abnormal_flag'] = $res['abnormal_flag'];
			$LabReports['result'][$i]['status'] = $res['status'];
			
			if($res['result_date']!='0000-00-00'){
				$result_date = date('m-d-Y',strtotime($res['result_date']));
			}
			else{
				$result_date = '00-00-0000';
			}
			
			$LabReports['result'][$i]['result_date'] = $result_date;
			$LabReports['result'][$i]['result_time'] = $res['result_time'];
			$LabReports['result'][$i]['result_comments'] = $res['result_comments'];
			
			if(!empty($res['result'])){
				$LabReports['result'][$i]['url'] = "http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=".$res['result_loinc']."&mainSearchCriteria.v.cs=2.16.840.1.113883.6.1&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en";
			}
			else{
				$LabReports['result'][$i]['url'] = "";
			}
			$i++;
		}
	}
	else{
		$LabReports['result'] = array();
	}			
	
	return json_encode($LabReports);
});


/* Lab Results & Values */
$this->respond(array('POST','GET'), '/labResults', function($request, $response, $service, $app) use(&$patientId){
	$med_obj = new IMW\MEDHX($app->dbh);
	$where = $service->__get('whereCondition');
	$pt_lab_results = $med_obj->getPatientLabOrdered($patientId,$where);
	if($pt_lab_results === false) $pt_lab_results = 'No Lab Results';
	$response = array('LaboratoryTests' => $pt_lab_results);
	return json_encode($response);
});

/* Vital Signs */
$this->respond(array('POST','GET'), '/vitalSigns', function($request, $response, $service, $app) use(&$patientId){
	$med_obj = new IMW\MEDHX($app->dbh);
	$tmp_arr = array();
	$where = $service->__get('whereCondition');
	$vs_arr = $med_obj->getVitalSigns($patientId,$where);
	
	$response = array('VitalSigns' => '');
	if(count($vs_arr) > 0){
		$tmp_arr = $vs_arr;	
	}
	//if(count($tmp_arr) == 0) $tmp_arr = 'No Vital signs';
	$response = array('VitalSigns' => $tmp_arr);
	return json_encode($response);
});

/* Immunizations */
$this->respond(array('POST','GET'), '/immunizations', function($request, $response, $service, $app) use(&$patientId){
	$tmp_arr =array();
	$where = $service->__get('whereCondition');
	$where = str_ireplace('timestamp','DATE(administered_date)',$where);
	$qry_immu = "
				SELECT
					immunization_id as ImmunizationId,
					immunization_cvx_code as CvxCode,
					ndc_code as NDCCode,
					DATE_FORMAT(administered_date, '%m-%d-%Y') as Date,
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
	//if(count($tmp_arr) == 0) $tmp_arr = 'No Immunizations';
	$response = array('Immunizations' => $tmp_arr);
	return json_encode($response);
});

/* Assessment & Plan of Treatment */
$this->respond(array('POST','GET'), '/assessmentPlan', function($request, $response, $service, $app) use(&$patientId){
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
					$tmp_arr2['Assessment'] = trim($assess_text_part);
				}
				
				if(empty($obj['plan']) == false){
					$tmp_arr2['Plans'] = trim(htmlentities($obj['plan']));
				}
				$tmp_arr[] = $tmp_arr2;
				unset($tmp_arr2);
			}
		}
		$return_data[$assess_array['dos']] = $tmp_arr;
	}
	
	$response = array('AssessmentPlan' => $return_data);
	$response = json_encode($response);
	// Data Formatted According to IOS Developer //
	$response = (str_ireplace('\n','',$response));
	$response = (preg_replace('/\s+/',' ', $response));
	return ($response);
});

/* Patient Goals */
$this->respond(array('POST','GET'), '/patientGoals', function($request, $response, $service, $app) use(&$patientId){
	$where = $service->__get('whereCondition');
	$where = str_ireplace('timestamp','DATE(`goal_date`)',$where);
	
	$goal_qry = 'SELECT `id`, `patient_id`, `form_id`, `goal_set`, `loinc_code`,
						`goal_data`, `goal_data_type`, `gloal_data_type_unit`, `operator_id`,
						DATE_FORMAT(goal_date, "%m-%d-%Y") as goal_date,
						DATE_FORMAT(goal_date, "%m-%d-%Y") as goal_date_show
					from patient_goals 
					where  patient_id="'.$patientId.'" AND delete_status = 0 '.$where.'';
	
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
	
	$response = array('PatientGoals' => $returnArr);
	return json_encode($response);
});

/* Health & Concerns */
$this->respond(array('POST','GET'), '/healthConcerns', function($request, $response, $service, $app) use(&$patientId){
	$resp_observation = $resp_concern = $resp_rel_observation = array();
	$where = $service->__get('whereCondition');
	$where = str_ireplace('timestamp','DATE(hco.observation_date)',$where);
	$sql = "
		SELECT
			hco.id as ID,
			hco.snomed_code as Code,
			hco.observation_date as ObservationDate,
			hco.observation as Observation,
			DATE_FORMAT(hco.observation_date, '%m-%d-%Y') as ObservationDate,
			
			hcc.id as ConcernID,
			hcc.concern as Concern,
			DATE_FORMAT(hcc.concern_date, '%m-%d-%Y') as ConcernDate,
			hcc.status as ConcernStatus,
			
			hcr.id as RelConcernID,
			hcr.snomed_code as RelCode,
			hcr.rel_observation as RelObservation,
			DATE_FORMAT(hcr.rel_observation_date, '%m-%d-%Y') as RelObservationDate
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
				$concern['Date'] = (empty($row['ConcernDate']) == false && $row['ConcernDate'] != '00-00-0000') ? $row['ConcernDate'] : '';
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
				$Relconcern['Date'] = (empty($row['RelObservationDate']) == false && $row['RelObservationDate'] != '00-00-0000') ? $row['RelObservationDate'] : '';
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
// CC history AND Assessment Plan AND Problem List //
$this->respond(array('POST','GET'), '/getCCHistory', function($request, $response, $service,$app) use(&$patientId){
	$result = array();
	$response =array();
	$qryDocInner = "SELECT cc_id, DATE_FORMAT(date_of_service, '%m-%d-%Y') as date, ccompliant, reason FROM `chart_left_cc_history` WHERE `patient_id` = '".((int)$patientId)."' ORDER BY form_id desc" ;
	$recordInner = $app->dbh->imw_query($qryDocInner);
	while($fetchInner = $app->dbh->imw_fetch_assoc($recordInner)){
		$result[] = $fetchInner;
	}
	
	$response['CCHistory'] =  $result;
	
	$prob_list_arr = array();
	//$where = str_ireplace('timestamp','DATE(onset_date)',$service->__get('whereCondition'));
	/* Problem List Data */
	$prob_qry = "
			SELECT 
				id as ProblemID,
				user_id as ProviderID,
				problem_name as ProblemName,
				prob_type as ProblemType,
				ccda_code as Code,
				status as Status,
				DATE_FORMAT(onset_date, '%m-%d-%Y') as OnsetDate,
				DATE_FORMAT(timestamp, '%m-%d-%Y') as LastModifiedDate
			FROM 
				pt_problem_list
			WHERE 
				pt_id = ".$patientId." AND
				problem_name != '' AND
				LOWER(status) IN ('active','completed') AND
				form_id = 0
				".$where;
		
	$res = $app->dbh->imw_query($prob_qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$row['ProblemName'] = filter_var($row['ProblemName'],FILTER_SANITIZE_STRING);
			$row['ProblemType'] = filter_var($row['ProblemType'],FILTER_SANITIZE_STRING);
			$row['Status'] = filter_var($row['Status'],FILTER_SANITIZE_STRING);
			
			if(stristr($row['ProblemName'],"-")){$problemNameExp = explode('-',$row['ProblemName']);
			
				if (stristr($row['ProblemName'],"("))
				{
				     $problemNameExp = explode('(',str_ireplace(')',"",$row['ProblemName']));
				}
			}
			
			elseif(stristr($row['ProblemName'],"("))
			{
				$problemNameExp = explode('(',str_ireplace(')',"",$row['ProblemName']));
			}
			
			$problem_code = trim(end($problemNameExp));
			//$row['url'] = "https://connect.medlineplus.gov/application?mainSearchCriteria.v.c=".$problem_code."&mainSearchCriteria.v.cs=2.16.840.1.113883.6.103&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en";
			$row['problem_code'] =  $problem_code;
			
			$prob_list_arr[] = $row;
		}
		
		$app->dbh->imw_free_result($res);
		unset($res);
	}
	
	$response['ProblemList'] = $prob_list_arr;
	
	$qryAccess = "select form_id,assess_plan from chart_assessment_plans where patient_id = ".$patientId. " ORDER BY form_id desc LIMIT 1 ";
	$resAccess = $app->dbh->imw_query($qryAccess);
	$rowAccess = $app->dbh->imw_fetch_assoc($resAccess);
	
	$finalExternal=simplexml_load_string($rowAccess['assess_plan'], null , LIBXML_NOCDATA);
	$accessArray = json_decode(json_encode($finalExternal) , true);
	//print_r($accessArray);
	$planArray = array();
	$i=0;
	foreach($accessArray['data']['ap'] as $dataValue){
		$vAssess='';
		$arrAssSplit = preg_split("/\([^\(]*$/",$dataValue['assessment']);
		$vAssess = $arrAssSplit[0];
		$planArray[$i]['assessment'] = $vAssess;
		$planArray[$i]['plan'] = !is_array($dataValue['plan']) ? $dataValue['plan'] : '';
		$i++;
	}
	$response['AssessmentPlan'] = $planArray;
	/*Date of service*/
	$qryAccess = "select date_of_service from chart_master_table where id = '".$rowAccess['form_id']. "'  AND delete_status != 1";
	$resAccess = $app->dbh->imw_query($qryAccess);
	$get_date = $app->dbh->imw_fetch_assoc($resAccess);
	if($get_date['date_of_service']!="0000-00-00"){
		$response['c_plans_date_of_service'] = date('m-d-Y',strtotime($get_date['date_of_service']));
	}
	else{
		$response['c_plans_date_of_service']= "00-00-0000";
	}
	$response = json_encode($response);
	$response = (str_ireplace(array('\n','\r'),' ',$response));
	$response = (preg_replace('/\s+/',' ', $response));
	return ($response);
});
	
$this->respond(function($request, $response, $service) use(&$patientId) {

});