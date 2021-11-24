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
 * Purpose: Rotes for generating Review of System
*/

$this->respond(array('GET'), '*', function($request, $response, $service, $app) {
	$returnData = $medData = $tmp_arr = array();
	$patientId = $dos = '';
	$debugVal = false; // Changes to true to create HTML and PDF file for debugging if requested in API
	$format = "m-d-Y";
	
	/* Validating Values */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt();
	$patientId = filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	$service->validateParam('dateOfService', 'Please provide valid date.')->notNull()->isDate();
	$dos = $app->dbh->imw_escape_string( $request->__get('dateOfService') );
	
	/* If debug request is from API request */
	if($request->__isset('debug')){
		$debugVal = true;
	}
	
	$med_obj = new IMW\MEDHX($app->dbh);
	$form_id = $med_obj->get_form_id($dos, $patientId);
	
	if($form_id === false){
		$response->append('Invalid DOS provided.');
		$this->abort(400);
	}
	
	$arrSites = array(1 => 'OS', 2 => 'OD', 3 => 'OU', 4 => 'PO');
	$drugType = array('fdbATDrugName' => 'Drug', 'fdbATIngredient' => 'Ingredient', 'fdbATAllergenGroup' => 'Allergen');
	$procType = array("surgery" => "Surgery", "procedure" => "Procedure", "intervention" => "Intervention");
	
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
								if(strtolower($data['allergy_status']) != 'deleted'){
									unset($tmp_arr);
									$tmp_arr['MedName'] = $data['title'];
									$tmp_arr['Sig'] = $data['sig'];
									$tmp_arr['Dosage'] = $data['destination'];
									$tmp_arr['BeginDate'] = (empty($data['begdate']) == false && $data['begdate'] != "0000-00-00") ? $med_obj->convertDate($data['begdate']) : '';
									$tmp_arr['EndDate'] = (empty($data['enddate']) == false && $data['enddate'] != "0000-00-00") ? $med_obj->convertDate($data['enddate']) : '';
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
								if(strtolower($data['allergy_status']) == 'active' || strtolower($data['allergy_status']) == 'completed'){
									unset($tmp_arr);
									$tmp_arr['Name'] = $data['title'];
									$tmp_arr['BeginDate'] = (empty($data['begdate']) == false && $data['begdate'] != "0000-00-00") ? $med_obj->convertDate($data['begdate']) : '';
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
									$tmp_arr['ProcedureDate'] = (empty($data['begdate']) == false && $data['begdate'] != "0000-00-00") ? $med_obj->convertDate($data['begdate']) : '';
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
							
							case 9:
								if(strtolower($data['allergy_status']) != 'deleted'){
									//$medData['UDI'][$data['parent_id']] = $data; //UDI
								}
							break;
						}
						unset($tmp_arr);
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
				$arrTemp['ModifiedDate'] = date($format, strtotime($arrOcular['timestamp']));
				$arrTemp['PtCondition'] = $arrTemp['any_conditions_you_new'].$arrTemp['any_conditions_relative_new'];
				
				if(count($med_obj->getOcularData($arrTemp)) > 0){
					$medData['Ocular'] = $med_obj->getOcularData($arrTemp);
				}
			}
			
			/* General Health */
			$arrGenMed = unserialize($row['general_medicine']);
			if(count($arrGenMed) > 0){
				$genData = $med_obj->getGenHealth($arrGenMed);
				if($genData !== false && count($genData) > 0){
					$medData['GeneralHealth'] = $genData;
				}
			}
			
			/* Patient Blood Sugar */
			$arrBloodSugar = unserialize($row['patient_blood_sugar']);
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
			$arrPtCholesterol = unserialize($row['patient_cholesterol']);
			if(count($arrPtCholesterol) > 0){
				$tmp_arr = array();
				$counter = 0;
				foreach($arrPtCholesterol as $obj){
					if($counter == 0){
						$arr_tmp = array();
						$create_date = $create_time = '';
						$creation_date = strtotime($obj['creation_date']);
						list($create_date,$create_time) = explode('||', date(''.$format.'||Gi.s', $creation_date));
						
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
			$arrSocialHistory = unserialize($row['social_history']);
			if(count($arrSocialHistory) > 0){
				$status = $code = '';
				$tmp_arr = array();
				list($status,$code) = explode('/',$arrSocialHistory['smoking_status']);
				$tmp_arr['SmokingStatus'] = trim($status);
				$tmp_arr['SatusCode'] = trim($code);
				$medData['SocialHistory'] = $tmp_arr;
			}
			
			/* Immunizations */
			$arrImmunizations = unserialize($row['immunizations']);
			if(count($arrImmunizations) > 0){
				$tmp_arr = array();
				
				foreach($arrImmunizations as $obj){
					$arr_tmp = array();
					$immu_id = $immu_text = '';
					$arr_tmp['Date'] = (empty($obj['administered_date']) == false && $obj['administered_date'] != '0000-00-00') ? $med_obj->convertDate($obj['administered_date']) : '';
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
			$arrPtProblem = unserialize($row['pt_problem_list']);
			if(count($arrPtProblem) > 0){
				foreach($arrPtProblem as $obj){
					if($obj['status'] != 'Deleted'){
						$tmp_arr = array();
						$tmp_arr['ProblemName'] = $obj['problem_name'];
						$tmp_arr['ProblemCode'] = $obj['ccda_code'];
						$tmp_arr['OnSetDate'] = $med_obj->convertDate($obj['onset_date']);
						$tmp_arr['Status'] = $obj['status'];
						$tmp_arr['Type'] = $obj['prob_type'];
						$medData['PatientProblems'][] = $tmp_arr;
					} 
				}
			}
		}
	}
	
	
	$pdfStr = $med_obj->getReviewHtml($medData, $patientId, $dos, $debugVal);
	
	
	$service->__set('med_arr',$pdfStr);
});
 
//Hack to Accept blank  subCategory/
$this->respond(array('GET'), '', function(){});
	
$this->respond(function($request, $response, $service) use(&$patientId) {
	$main_arr = $service->__get('med_arr');
	
	return $main_arr;
});