<?php
/**
 * index.php
 * Access Type: InClude
 * Purpose: Routes for generating Review of System
*/

$this->respond(array('GET'), '*', function($request, $response, $service, $app) {
	$returnData = $medData = array();
	$patientId = $dos = '';
	
	/* Validating Values */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt();
	$patientId = filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	$service->validateParam('DateOfService', 'Please provide valid date.')->notNull()->isDate();
	$dos = $app->dbh->imw_escape_string( $request->__get('DateOfService') );
	
	$med_obj = new IMW\MEDHX($app->dbh);
	$form_id = $med_obj->get_form_id($dos, $patientId);
	
	if($form_id === false){
		$response->append('Invalid DOS provided.');
		$this->abort(400);
	}
	
	$data_qry = "SELECT 
					* 
				FROM  
					chart_genhealth_archive 
				WHERE
					patient_id='".$patientId."' AND
					form_id = '".$form_id."'";
					
	$res = $app->dbh->imw_query($data_qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			/* Medications -- Allergies -- Sx/Procedures -- UDI */
			$arrList = unserialize($row['lists']);
			if(count($arrList) > 0){
				foreach($arrList as $arrData){
					foreach($arrData as $data){
						switch($data['type']){
							case 1:
							case 4:
								if($data['allergy_status'] != 'Deleted'){
									$medData['Medications'][] = $data;	//Medications
								}
							break;
							
							case 3:
							case 7:
								if($data['allergy_status'] == 'Active' || $data['allergy_status'] == 'Completed'){
									$medData['Allergies'][] = $data;	//Allergies
								}
							break;
							
							case 5:
							case 6:
								if($data['allergy_status'] != 'Deleted'){
									$medData['Procedures'][] = $data;	//Procedures
								}
							break;	
							
							case 9:
								if($data['allergy_status'] != 'Deleted'){
									$medData['UDI'][$data['parent_id']] = $data; //UDI
								}
							break;
						}
					}
				}
			}
			
			/* Ocular health */
			$arrOcular = unserialize($row['ocular']);
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
				$arrTemp['ModifiedDate'] = date('Y-m-d', strtotime($arrOcular['timestamp']));
				$arrTemp['PtCondition'] = $arrTemp['any_conditions_you_new'].$arrTemp['any_conditions_relative_new'];
				
				$medData['Ocular'] = $med_obj->getOcularData($arrTemp);
			}
			
			/* General Medicine */
			$arrGenMed = unserialize($row['general_medicine']);
			if(count($arrGenMed) > 0){
				$genData = $med_obj->getGenHealth($arrGenMed);
				if($genData !== false){
					$medData['GeneralMedicine'] = $genData;
				}
			}
			
			/* Patient Blood Sugar */
			$arrBloodSugar = unserialize($row['patient_blood_sugar']);
			if(count($arrBloodSugar) > 0){
				$medData['PtBloodSugar'] = $arrBloodSugar;
			}
			
			/* Patient Cholesterol */
			$arrPtCholesterol = unserialize($row['patient_cholesterol']);
			if(count($arrPtCholesterol) > 0){
				$medData['PtCholesterol'] = $arrPtCholesterol;
			}
			
			/* Social History */
			$arrSocialHistory = unserialize($row['social_history']);
			if(count($arrSocialHistory) > 0){
				$medData['SocialHistory'] = $arrSocialHistory;
			}
			
			/* Immunizations */
			$arrImmunizations = unserialize($row['immunizations']);
			if(count($arrImmunizations) > 0){
				$medData['Immunizations'] = $arrImmunizations;
			}
			
			/* Pt. Problem List */
			$arrPtProblem = unserialize($row['pt_problem_list']);
			if(count($arrPtProblem) > 0){
				foreach($arrPtProblem as $obj){
					if($obj['status'] != 'Deleted'){
						$medData['PatientProblems'][] = $obj;
					} 
				}
			}
		}
	}
	
	print_r($medData);
	exit;
});
 
//Hack to Accept blank  subCategory/
$this->respond(array('GET'), '', function(){});
	
$this->respond(function($request, $response, $service) use(&$patientId) {
	//$main_arr = $service->__get('MainResponseContainer');
	//array_walk_recursive($main_arr, $converToString);
	
	//return json_encode($main_arr);
});