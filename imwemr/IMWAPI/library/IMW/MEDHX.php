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
*/

namespace IMW;

use HTML2PDF;
use DateTime;

/**
 * MEDHX
 *
 * Main MED HX Class
 */
class MEDHX
{
	public $delimiter = '~|~';
	public $dbh_obj = '';
	public $current_sec = 1;
	
	public function __construct($db_obj = '',$cur_sec = 1){
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		if(empty($cur_sec) == false){
			$this->current_sec = $cur_sec;
		}
	}
	
	public function get_form_id($dos, $patient_id){
		$return_data = false;
		$qry = $this->dbh_obj->imw_query("SELECT id FROM chart_master_table where date_of_service = '".$dos."' AND patient_id = ".$patient_id." ORDER BY date_of_service DESC LIMIT 1");
		if($qry && $this->dbh_obj->imw_num_rows($qry) > 0){
			$row = $this->dbh_obj->imw_fetch_assoc($qry);
			$return_data = $row['id'];	
		}
		return $return_data;
	}
	
	public function getOcularData($response = ''){
		if(empty($response)) return;
		$return_arr = array();
		$field_arr = $this->getPtRelVal($response["PtCondition"],$this->delimiter);
		$description_arr = $this->getPtRelVal($response["ChronicDescriptions"],$this->delimiter,true);
		$relatives_arr = $this->getFieldVal($response["chronicRelatives"]);
		
		foreach($field_arr as $key => $val){
			foreach($val as $val_key => $value){
				$tmp_arr = array();
				$val_key = ($val_key == 0) ? 'other' : $val_key; 
				$tmp_arr['ProblemId'] = $response['OcularId'].$value['id'];
				$tmp_arr['Name'] = $value['name'];
				$tmp_arr['Description'] = (empty($description_arr[$key][$val_key]) == false) ? $description_arr[$key][$val_key] : '';
				if($key == 'Relatives'){
					$relation_str = explode(',',$relatives_arr[$val_key]);
					$relation_str = array_filter($relation_str,'strlen');
					$tmp_arr['Relations'] = $relation_str;
				}
				$tmp_arr['LastModifiedDate'] = $response['ModifiedDate'];
				$return_arr[$key][] = $tmp_arr;
			}
		}
		return $return_arr;
	}
	
	//Returns array of patient and relative problems
	public function getPtRelVal($dbValue = '',$delimiter = "~|~",$view = false){
		$dbValue 	= trim($dbValue);	
		$delimiter	= trim($delimiter);
		$return_arr = array();
		
		if(stristr($dbValue,$delimiter)){
			list($pat,$rel) = explode($delimiter,$dbValue);
			if($view == true){
				if(empty($pat) == false){
					$return_arr['Patient'] = $this->getFieldVal($pat);
				}
				if(empty($rel) == false){
					$return_arr['Relatives'] = $this->getFieldVal($rel);
				}
			}else{
				if(empty($pat) == false){
					$return_arr['Patient'] = $this->getFieldsName($pat,1,$this->current_sec);
				}
				if(empty($rel) == false){
					$return_arr['Relatives'] = $this->getFieldsName($rel,2,$this->current_sec);
				}
			}
			
		}
		return $return_arr;
	}
	
	//Returns field name of provided string
	public function getFieldsName($str = '',$fields_for = 1,$section = 1,$view = false){
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
		$res = $this->dbh_obj->imw_query($qry);
		if($res && $this->dbh_obj->imw_num_rows($res) > 0){
			while($row = $this->dbh_obj->imw_fetch_assoc($res)){
				$tmp_arr = array();
				$tmp_arr['name'] = $row['name'];
				$tmp_arr['id'] = $row['id'];
				$return_arr[$row['field_key']] = $tmp_arr;
			}
		}
		
		return $return_arr;
	}
	
	//Returns Fields value
	public function getFieldVal($str = ''){
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
	public function getGenHealth($data = array()){
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
				$respKey = $this->dbh_obj->imw_query($sqlKey);
				while( $field_row = $this->dbh_obj->imw_fetch_assoc($respKey) )
				{
					$fieldKey = array_pop($field_row);
					$ptCondiftionsKeys[$fieldKey] = $field_row;
				}
				
				/*Relatives Medical Conditions*/
				$relCondiftionsKeys = array(); 
				$sqlKey = 'SELECT `id`, `name`, `field_key` FROM `fmh_med_fields` WHERE `type`=2 AND `sec_id`=2';
				$respKey = $this->dbh_obj->imw_query($sqlKey);
				while( $med_field_row = $this->dbh_obj->imw_fetch_assoc($respKey) )
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
			$condition['ProblemId'] = $rowId.$ptCondiftionsKeys[$ptMedCondVal]['id'];
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
			if(count($condition) > 0) $condition['LastModifiedDate'] = $data['timeStamp'];
			array_push($ptMedContidions, $condition);
		}
		
		/*Process Patient Medical conditions*/
		foreach( $relMedCondVals as $relMedCondVal )
		{
			$relMedCondVal = (int)$relMedCondVal;
			
			$condition = array();
			$condition['ProblemId'] = $rowId.$relCondiftionsKeys[$relMedCondVal]['id'];
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
			if(count($condition) > 0) $condition['LastModifiedDate'] = $data['timeStamp'];
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
	
	public function getPatientLabOrdered($pid = '', $where = ''){
		if(empty($where) == false){
			$where = str_ireplace('timestamp','lor.start_date',$where);
		}
		
		$q = "SELECT 
				lor.lab_test_id as LabId,
				lor.service as LabTestName,
				lor.loinc as Code,
				DATE(start_date) AS Date
			FROM 
				lab_observation_requested lor 
				JOIN lab_test_data ltd ON (lor.lab_test_id = ltd.lab_test_data_id) 
			WHERE 
				ltd.lab_patient_id = '".$pid."' 
				AND ltd.lab_status IN(1,2) 
				AND lor.del_status = 0 
				".$where."
				";	
		$res = $this->dbh_obj->imw_query($q);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$lab_ordered = array();
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$rs['CodeSystem'] = 'LOINC';
				$rs['LabDetails'] = $this->getLabOrderDestination($rs['LabTestName']);
				$rs['TestValues'] = $this->getPatientLabResults($rs['LabId'],$pid);
				$lab_ordered[] = $rs;
			}
			return $lab_ordered;
		}else{
			return false;
		}
	}
	
	public function getLabOrderDestination($labTestTitle){
		$q = "SELECT 
				lab_contact_name as ContactName,
				lab_radiology_phone as Phone,
				lab_radiology_address as Address,
				lab_radiology_city as City,
				lab_radiology_state as State, 
				lab_radiology_zip as Zip 
			FROM 
				`lab_radiology_tbl` 
			WHERE 
				`lab_radiology_name` LIKE '".$labTestTitle."' 
				AND 
			 lab_radiology_status='0' LIMIT 1";
		$res = $this->dbh_obj->imw_query($q);
		if($res && $this->dbh_obj->imw_num_rows($res)>0){
			$rs = $this->dbh_obj->imw_fetch_assoc($res);
			return $rs;
		}
		return false;
	}
	
	public function getPatientLabResults($labId,$pid){
		$q = "SELECT 
				lor.result_loinc as ResultCode,
				lor.observation as Type,
				lor.result as Value,
				CONCAT(lor.result_range,' ',lor.uom) as RefRange,
				DATE(result_date) AS Date
			FROM 
				lab_observation_result lor 
				JOIN lab_test_data ltd ON (lor.lab_test_id = ltd.lab_test_data_id) 
			WHERE 
				lor.lab_test_id='".$labId."' AND 
				ltd.lab_patient_id = '".$pid."' AND 
				ltd.lab_status IN(1,2)";
		$res = $this->dbh_obj->imw_query($q);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$lab_ordered = array();
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$rs['CodeSystem'] = 'LOINC';
				$lab_ordered[] = $rs;
			}
			return $lab_ordered;
		}else{
			return false;
		}
	}
	
	public function getVitalSigns($pid = '',$where = ''){
		$vs_val_arr = $response = array();
		$vs_limit_sql = "SELECT id,vital_sign from vital_sign_limits WHERE status = 1";
		$res = $this->dbh_obj->imw_query($vs_limit_sql);
		if($res && $this->dbh_obj->imw_num_rows($res) > 0){
			while($row = $this->dbh_obj->imw_fetch_assoc($res)){
				$row['vital_sign'] = preg_replace('/[^\da-z]/i', '', $row['vital_sign']);
				$vs_val_arr[$row['id']] = $row['vital_sign'];
			}
		}
		
		if(empty($where) == false){
			$where = str_ireplace('timestamp','DATE(vsm.date_vital)',$where);
		}
		$sql = "SELECT 
					vsp.range_vital as RangeVital,
					vsp.unit as Unit,
					vsl.vital_sign,
					vsp.vital_sign_id,
					DATE(vsm.date_vital) as Date 
				FROM 
					vital_sign_master vsm 
					JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
					JOIN vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
				WHERE 
					vsm.patient_id = '".$pid."' AND  
					vsm.status = 0 
					".$where."
				ORDER BY vsp.id ASC";	
		$res = $this->dbh_obj->imw_query($sql);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$arrVSType = array(
					4 => "9279-1",
					5 => "59408-5",
					1 => "8480-6",
					2 => "8462-4",
					3 => "8867-4",
					6 => "8310-5",
					7 => "8302-2",
					8 => "29463-7",
					9 => "39156-5",
					10 => "",
					11 => "3150-0"
				);
			
			
			
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				if(empty($vs_val_arr[$rs['vital_sign_id']]) == false){
					$rs['Unit'] = html_entity_decode($rs['Unit']);
					$rs['Code'] = $arrVSType[$rs['vital_sign_id']];
					$response[$vs_val_arr[$rs['vital_sign_id']]][] = $rs;
				}
			}
		}
		return $response;
	}
	
	public function valuesNewRecordsAssess($where,$patient_id,$sel=" * ",$LF="0",$flgmemo=1){
		$return_arr = array();
		$where = str_ireplace('timestamp','chart_master_table.date_of_service',$where);
		$strmemo = ($flgmemo==1) ? "AND chart_master_table.memo != '1' " : "" ;
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$qry = "SELECT * FROM chart_master_table ".
			  "INNER JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id ".
			  "WHERE chart_master_table.patient_id = '$patient_id' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			  "AND chart_master_table.record_validity = '1' ".
			  $strmemo. //do not get memo assessments and plans: 08-04-2014
			  $LF.
			  $where.
			  "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.create_dt DESC, chart_master_table.id DESC";

		$res = $this->dbh_obj->imw_query($qry);
		if($this->dbh_obj->imw_num_rows($res)>0){
			while($rs = $this->dbh_obj->imw_fetch_assoc($res)){
				$tmp_arr = array('assess_plan' => $rs['assess_plan'], 'dos' => $rs['date_of_service']);
				$return_arr[] = $tmp_arr;
			}
		}
		return $return_arr;
	}
	
	function show_age($dob){
		$yr="";
		if(!empty($dob)&&($dob!="0000-00-00")){
			$dob_time = strtotime($dob);
			$cur_time = strtotime(date("Y-m-d H:i:s"));
			$age_days = floor(($cur_time-$dob_time)/(60*60*24));
			if($age_days >= 365)
			{
				/*The auto text says 62 yrs old the physicians pet peeve the correct term is  yr old. not yrs.*/
				$yrs_tmp = $age_days/365.25;
				$yrs = floor($yrs_tmp);
				$age = $yrs." Yr.";
			}
			else if($age_days >= 30)
			{
				$months = floor($age_days/30);
				$age = $months." Mon.";
			}
			else if($age_days > 0)
			{
				$age = $age_days." Days";
			}
		}
		return $age;	
	}
	
	function core_address_format($street, $street2, $city, $state, $postalCode){
		$fullAddress='';
		$prevStreet					=	'';
		$prevStreet2				=	'';
		$prevCityStateZip			=	'';
		if(trim($street) || trim($street2) || trim($city) || trim($state) || trim($postalCode)) {
			if(trim($street)) {
				$prevStreet		=	stripslashes($street).', ';
			}	
			if(trim($street2)) {
				$prevStreet2		=	stripslashes($street2).', ';
			}
			if(trim($city) || trim($state) || trim($postalCode)) {
				$prevCityStateZip	=	stripslashes($city).', '.stripslashes($state).' '.stripslashes($postalCode).'<br>';
				$prevCityStateZip 	= 	trim($prevCityStateZip);
			}
			$fullAddress	=	trim($prevStreet.$prevStreet2.$prevCityStateZip);
		}
		return $fullAddress;
	}
	
	public function getpatientDetails($id = ''){
		if(empty($id)) return false;
		$returnArr = array();
		$query = "
			SELECT 
				id,
				fname,
				mname,
				sex,
				lname,
				dob,
				street,
				street2,
				postal_code,
				phone_home,
				city,
				state
			FROM 
				patient_data 
			WHERE 
				id = $id";
		$res = $this->dbh_obj->imw_query($query);
		if($this->dbh_obj->imw_num_rows($res)>0){
			$row = $this->dbh_obj->imw_fetch_assoc($res);
			$returnArr['Name'] = $row['lname'].', '.$row['fname'].' '.$row['mname'].' - '.$row['id'];
			$returnArr['dob'] = $row['dob'];
			$returnArr['age'] = $this->show_age($row['dob']);
			$returnArr['street1'] = $row['street1'];
			$returnArr['street2'] = $row['street2'];
			$returnArr['zip'] = $row['postal_code'];
			$returnArr['city'] = $row['city'];
			$returnArr['state'] = $row['state'];
			$returnArr['phone'] = $row['phone_home'];
			$returnArr['sex'] = $row['sex'];
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
		);
		
		if(isset($sectionArr[$key]) && empty($sectionArr[$key]) == false){
			$return = ($sectionArr[$key] == 1) ? true : false;
		}
		return $return;}
	
	function getReviewHtml($arrData = array(), $patientId = '', $dateOfService = '', $debugging = false){
		if(count($arrData) == 0) return false;
		$patientDetails = $this->getpatientDetails($patientId);
		$pdf_body_str = '';
		
		//Medications
		if(isset($arrData['Medications']) && count($arrData['Medications']) > 0 && $this->showThis('Medications')){
			$bodyStr = $ocuStr = $sysStr = $td_wrap = '';
			$counter = 0;
			foreach($arrData['Medications'] as $key => $val){
				switch($key){
					case 'Systemic':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'Medications - '.$key.'', 'MedicationsSys');
						}
						$sysStr .= $headerStr;
						$sysStr .= $this->getHtmlBody($val, 'MedicationsSys');
						$counter = 0;
					break;
					
					case 'Ocular':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'Medications - '.$key.'', 'MedicationsOcu');
						}
						$ocuStr .= $headerStr;
						$ocuStr .= $this->getHtmlBody($val, 'MedicationsOcu');
						$counter = 0;
					break;
				}
			}	
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$sysStr.'</table></td></tr>';
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$ocuStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;	
		}
		
		//Allergies
		if(isset($arrData['Allergies']) && count($arrData['Allergies']) > 0  && $this->showThis('Allergies')){
			$bodyStr = '';
			
			$headerStr = $this->getHtmlHeader(reset($arrData['Allergies']), true, 'Allergies', 'Allergies');
			$bodyStr .= $headerStr;
			$bodyStr .= $this->getHtmlBody($arrData['Allergies'], 'Allergies');
			
			$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;	
		}
		
		
		//Procedures
		if(isset($arrData['Procedures']) && count($arrData['Procedures']) > 0 && $this->showThis('Procedures')){
			$bodyStr = $sysStr = $ocuStr = $td_wrap = '';
			$counter = 0;
			foreach($arrData['Procedures'] as $key => $val){
				switch($key){
					case 'Systemic':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'Procedures - '.$key.'', 'ProceduresSys');
						}
						$sysStr .= $headerStr;
						$sysStr .= $this->getHtmlBody($val, 'ProceduresSys');
						$counter = 0;
					break;
					
					case 'Ocular':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'Procedures - '.$key.'', 'ProceduresOcu');
						}
						$ocuStr .= $headerStr;
						$ocuStr .= $this->getHtmlBody($val, 'ProceduresOcu');
						$counter = 0;
					break;
				}
			}	
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$sysStr.'</table></td></tr>';
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$ocuStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;		
		}
		
		//Ocular
		if(isset($arrData['Ocular']) && count($arrData['Ocular']) > 0 && $this->showThis('Ocular')){
			$relStr = $patRel = $td_wrap = '';
			$counter = 0;
			foreach($arrData['Ocular'] as $key => $val){
				switch($key){
					case 'Patient':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'Ocular - '.$key.'', 'OcularPt');
						}
						$patRel .= $headerStr;
						$patRel .= $this->getHtmlBody($val, 'OcularPt');
						$counter = 0;
					break;
					
					case 'Relatives':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'Ocular - '.$key.'', 'OcularRel');
						}
						$relStr .= $headerStr;
						$relStr .= $this->getHtmlBody($val, 'OcularRel');
						$counter = 0;
					break;
				}
			}
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$patRel.'</table></td></tr>';
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$relStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;		
		}
		
		//GeneralHealth
		if(isset($arrData['GeneralHealth']) && count($arrData['GeneralHealth']) > 0 && $this->showThis('GeneralHealth')){
			$relStr = $patRel = $td_wrap = '';
			$counter = 0;
			foreach($arrData['GeneralHealth'] as $key => $val){
				switch($key){
					case 'Patient':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'General Health - '.$key.'', 'GeneralPt');
						}
						$patRel .= $headerStr;
						$patRel .= $this->getHtmlBody($val, 'GeneralPt');
						$counter = 0;
					break;
					
					case 'Relatives':
						$headerStr = '';
						if($counter == 0){
							$headerStr = $this->getHtmlHeader(reset($val), true, 'General Health - '.$key.'', 'GeneralRel');
						}
						$relStr .= $headerStr;
						$relStr .= $this->getHtmlBody($val, 'GeneralRel');
						$counter = 0;
					break;
				}
			}	
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$patRel.'</table></td></tr>';
			$td_wrap .= '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$relStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;		
		}
		
		//BloodSugar
		if(isset($arrData['BloodSugar']) && count($arrData['BloodSugar']) > 0 && $this->showThis('BloodSugar')){
			$bodyStr = '';
			
			$headerStr = $this->getHtmlHeader(reset($arrData['BloodSugar']), true, 'Blood Sugar', 'BloodSugar');
			$bodyStr .= $headerStr;
			$bodyStr .= $this->getHtmlBody($arrData['BloodSugar'], 'BloodSugar');
			
			$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;	
		}
		
		//Cholesterol
		if(isset($arrData['Cholesterol']) && count($arrData['Cholesterol']) > 0 && $this->showThis('Cholesterol')){
			$bodyStr = '';
			
			$headerStr = $this->getHtmlHeader(reset($arrData['Cholesterol']), true, 'Cholesterol', 'Cholesterol');
			$bodyStr .= $headerStr;
			$bodyStr .= $this->getHtmlBody($arrData['Cholesterol'], 'Cholesterol');
			
			$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;		
		}
		
		//SocialHistory
		if(isset($arrData['SocialHistory']) && count($arrData['SocialHistory']) > 0 && $this->showThis('SocialHistory')){
			$bodyStr = $td_wrap = '';
			
			$headerStr = $this->getHtmlHeader(reset($arrData['SocialHistory']), true, 'Social History', 'SocialHistory');
			$headerStr = str_ireplace('1','2',$headerStr);
			$bodyStr .= $headerStr;
			if(isset($arrData['SocialHistory']['SmokingStatus']) && isset($arrData['SocialHistory']['SatusCode']) && empty($arrData['SocialHistory']['SmokingStatus']) == false && empty($arrData['SocialHistory']['SatusCode']) == false){
				$bodyStr .= '<tr><td style="width:370px" class="bdrbtm bdrlft bdrrght marginBot">'.$arrData['SocialHistory']['SmokingStatus'].'</td><td style="width:367px" class="bdrbtm bdrlft bdrrght">'.$arrData['SocialHistory']['SatusCode'].'</td></tr>';
				$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
			}
			$pdf_body_str .= $td_wrap;		
		}
		
		//Immunizations
		if(isset($arrData['Immunizations']) && count($arrData['Immunizations']) > 0 && $this->showThis('Immunizations')){
			$bodyStr = '';
			
			$headerStr = $this->getHtmlHeader(reset($arrData['Immunizations']), true, 'Immunizations', 'Immunizations');
			$bodyStr .= $headerStr;
			$bodyStr .= $this->getHtmlBody($arrData['Immunizations'], 'Immunizations');
			
			$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;	
		}
		
		//PatientProblems
		if(isset($arrData['PatientProblems']) && count($arrData['PatientProblems']) > 0 && $this->showThis('PatientProblems')){
			$bodyStr = '';
			
			$headerStr = $this->getHtmlHeader(reset($arrData['PatientProblems']), true, 'Problem List', 'PatientProblems');
			$bodyStr .= $headerStr;
			$bodyStr .= $this->getHtmlBody($arrData['PatientProblems'], 'PatientProblems');
			
			$td_wrap = '<tr><td class="marginBot width_100"><table class="width_700" style="border-collapse:collapse;">'.$bodyStr.'</table></td></tr>';
			$pdf_body_str .= $td_wrap;	
		}
		
		$css = '
			<style>
				table{
					margin-bottom:5px;
				}
				
				.width_700{
					width:700px!important;
				}
				
				.width_100{
					width:700px!important;
				}
				
				.tb_headingHeader{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684ab;
				}
				
				.text_b_w{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
				}	
				
				.text_lable{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					font-weight:bold;
					vertical-align:middle;
				}
				.text_value{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:100;
					background-color:#FFFFFF;
				}	
				
				.bdrbtm{
					border-bottom:1px solid #C0C0C0;
					height:20px;	
					vertical-align:baseline;
				}
				.bdrtop{
					border-top:1px solid #C0C0C0;
				}
				.bdrrght{
					border-right:1px solid #C0C0C0;
					vertical-align:baseline;
				}
				
				.bdrlft{
					border-left:1px solid #C0C0C0;
					vertical-align:baseline;
				}
				
				.pd5{
					padding-top:5px;
					padding-bottom:5px;
				}	
				
				.marginBot{
					padding-bottom:5px;
				}
			</style>
		';
		
		$patient_address = $this->core_address_format(' ', ' ', $patientDetails['city'], $patientDetails['state'], $patientDetails['postal_code']);
		$pdfHeader = '
			<page_header>
				<table style="border-collapse:collapse;" border="0" cellspacing="0"  cellpadding="0">
					<tr>
						<td style="width:35%" class="tb_headingHeader">'.$patientDetails['Name'].'</td>
						<td style="width:30%" class="tb_headingHeader" align="center"><strong>Review of System</strong></td>
						<td style="width:35%; text-align:right" class="tb_headingHeader">Date of Service:&nbsp;'.$this->convertDate($dateOfService).'&nbsp;</td>
					</tr>
				</table>
			</page_header>

			<table style="width:100%;border-collapse:collapse" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width:40%" align="left" valign="top"> 
						<table style="" border="0" cellspacing="0"  cellpadding="0">
							<tr>
								<td style="width:100%" class="text_lable">'.$patientDetails['Name'].'</td>
							</tr>
							<tr>
								<td style="width:100%" class="text_value">'.$patientDetails['sex'].'&nbsp;('.$patientDetails['age'].')'.'&nbsp;'.$patientDetails['dob'].'&nbsp;</td>
							</tr>';
			 
				if($patientDetails['street1'] != ''){ 
					$pdfHeader .='<tr>
								<td style="width:100%" class="text_value">'.$patientDetails['street1'].'&nbsp;</td>
							</tr>';
				}
				if($patientDetails['street2'] != ''){		
					$pdfHeader .='<tr>
								<td style="width:100%" class="text_value">'.$patientDetails['street2'].'&nbsp; </td>
							</tr>';
					}
				if($patient_address != ''){			
					$pdfHeader .='<tr>
								<td style="width:100%" class="text_value">'.$patient_address.'</td>
							</tr>';
				}
				
					$pdfHeader .='<tr>
								<td style="width:100%" class="text_value">Ph.: '.$patientDetails['phone'].'&nbsp; </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>	
		';
		
		$pdfContent = $css.'
			<page backtop="5mm" backbottom="5mm">
				'.$pdfHeader.'
				<table style="width:700px;border-collapse:collapse;" border="0" align="center">'.$pdf_body_str.'</table>
			</page>';
		
		$html2pdf = new HTML2PDF('P','A4','en');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->WriteHTML($pdfContent);
		
		$b64Doc = $html2pdf->Output('', 'S');
		$b64Doc = base64_encode($b64Doc);
		
		return $b64Doc;
	}
	
	//Creates table heading rows for the provided array
	function getHtmlHeader($arrData = array(), $setTitle = false, $title = '', $callFrom = ''){
		if(count($arrData) == 0) return ;
		$widthArr = $this->getWidth($callFrom);
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
			$colString = '<tr><th class="tb_headingHeader pd5" colspan="'.count($arrData).'"><strong>'.$title.'</strong></th></tr>';
			$returnStr = $colString.$returnStr;
		}
		
		return $returnStr;
	}
	
	//Creates table data rows for the provided array
	function getHtmlBody($arrData = array(), $callFrom = ''){
		if(count($arrData) == 0) return ;
		
		$widthArr = $this->getWidth($callFrom);
		
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
				$returnStr .= '<td style="width:'.$widthVal.'px" class="bdrbtm bdrlft bdrrght">'.htmlspecialchars($val).'</td>';
				
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
				$returnArr['MedName'] = 100;
				$returnArr['Sig'] = 50;
				$returnArr['Dosage'] = 100;
				$returnArr['BeginDate'] = 100;
				$returnArr['EndDate'] = 100;
				$returnArr['Comments'] = 100;
				$returnArr['Code'] = 50;
				$returnArr['Status'] = 100;
			break;
			
			case 'MedicationsOcu':
				$returnArr['MedName'] = 100;
				$returnArr['Sig'] = 50;
				$returnArr['Dosage'] = 100;
				$returnArr['BeginDate'] = 85;
				$returnArr['EndDate'] = 100;
				$returnArr['Comments'] = 100;
				$returnArr['Code'] = 50;
				$returnArr['Status'] = 58;
				$returnArr['Site'] = 35;
			break;
			
			case 'Allergies':
				$returnArr['Name'] = 150;
				$returnArr['BeginDate'] = 100;
				$returnArr['Comments'] = 150;
				$returnArr['Type'] = 100;
				$returnArr['Status'] = 100;
				$returnArr['Code'] = 115;
			break;
			
			case 'ProceduresOcu':
				$returnArr['Name'] = 210;
				$returnArr['Code'] = 100;
				$returnArr['ProcedureDate'] = 100;
				$returnArr['Status'] = 100;
				$returnArr['Type'] = 100;
				$returnArr['Site'] = 105;
			break;
			
			case 'ProceduresSys':
				$returnArr['Name'] = 210;
				$returnArr['Code'] = 100;
				$returnArr['ProcedureDate'] = 150;
				$returnArr['Status'] = 100;
				$returnArr['Type'] = 162;
			break;	
				
			case 'OcularPt':
				$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 250;
				$returnArr['Description'] = 327;
				$returnArr['LastModifiedDate'] = 100;
			break;
			
			case 'OcularRel':
				$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 150;
				$returnArr['Description'] = 130;
				$returnArr['Relations'] = 285;
				$returnArr['LastModifiedDate'] = 100;
			break;
			
			case 'GeneralPt':
				$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 180;
				$returnArr['Description'] = 135;
				$returnArr['UnderControl'] = 250;
				$returnArr['Other'] = 100;
			break;
			
			case 'GeneralRel':
				$returnArr['ProblemId'] = 50;
				$returnArr['Name'] = 180;
				$returnArr['Description'] = 135;
				$returnArr['Relation'] = 250;
				$returnArr['Other'] = 100;
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
				$returnArr['Unit'] = 50;
				$returnArr['Site'] = 50;
				$returnArr['Notes'] = 100;
				$returnArr['RefusalReason'] = 100;
				$returnArr['Status'] = 50;
			break;
			
			case 'PatientProblems':
				$returnArr['ProblemName'] = 315;
				$returnArr['ProblemCode'] = 100;
				$returnArr['OnSetDate'] = 100;
				$returnArr['Status'] = 100;
				$returnArr['Type'] = 100;
			break;
			
		}
		return $returnArr;
	}
	
	function convertDate($date = '', $format = 'm-d-Y'){
		if(empty($date)) return ;
		
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
			$date = date($format, strtotime($date));
		}
		return $date;
	}
	
	//This function returns the Audited Data for the requested section
	function getAuditData($callFrom = '', $patientId = '', $startDate = '', $endDate = ''){
		if(empty($callFrom) || empty($patientId)) return false;
		$paramArr = $tmpValArr = $valArr = $finalOcuData = array();
		$where = '';
		
		if(empty($endDate) && empty($startDate) == false) $endDate = $startDate;
		
		if(empty($startDate) == false && empty($endDate) == false) $where = 'AND DATE(Date_Time) BETWEEN "'.$startDate.'" AND "'.$endDate.'" ';
		
		//Switch based on the requested section
		switch(strtolower($callFrom)){
			case 'ocular':
				$paramArr['category'] = 'ocular';
				$paramArr['mapFields'] = array(
					'ocular_id' => 'OcularId',
					'patient_id' => 'PatientId',
					'chronicDesc' => 'ChronicDescriptions',
					'chronicRelative' => 'chronicRelatives',
					'DATE(timestamp)' => 'ModifiedDate',
					'any_conditions_others_you' => 'otherCondition',
					'any_conditions_you' => 'ptCondition',
					'any_conditions_other_relative' => 'otherConditionRel',
					'any_conditions_relative' => 'RelCondition'
				);
			break;
		}
		
		//If $paramArr has something
		if(count($paramArr) > 0){
			$categoryBlock = '';
			$sqlFields = '*';
			
			//Category to get data of
			if(isset($paramArr['category']) && empty($paramArr['category']) == false) $categoryBlock = $paramArr['category'];
			
			//Mapping DB fields according to API fields 
			if(is_array($paramArr['mapFields']) && isset($paramArr['mapFields'])){
				$mapFieldsArr = &$paramArr['mapFields'];
				$mapFieldsArr = array_filter($mapFieldsArr);
				
				$tmpArr = array();
				foreach($mapFieldsArr as $dbField => &$apiField){
					//$str = $dbField;
					//if(empty($apiField) == false) $str .= ' AS '.$apiField;
					array_push($tmpArr, $dbField);
				}
				
				if(count($tmpArr) > 0) $sqlFields = implode("','", $tmpArr);
			}
			
			//If category is there and we have fields to get
			if(empty($categoryBlock) == false){
				$dbQry = imw_query('
					SELECT 
						Pk_Id as tableId,
						Data_Base_Field_Name as dbField,
						Old_Value as oldValue, 
						New_Value as newValue,
						Date_Time as AuditDate, 
						action as Action 
					FROM 
						audit_trail 
					WHERE 
						Data_Base_Field_Name IN (\''.$sqlFields.'\') AND 
						pid = '.$patientId.' AND 
						Query_Success = 0 AND 
						LOWER(Category_Desc) = \''.strtolower($categoryBlock).'\' 
						'.$where.'
					ORDER BY Date_Time DESC	
			  ');
				
				if($dbQry && imw_num_rows($dbQry) > 0){
					while($rowFetch = imw_fetch_assoc($dbQry)){
						$tempArr = array();
						$tempArr['TableID'] = (isset($rowFetch['tableId']) && empty($rowFetch['tableId']) == false) ? $rowFetch['tableId'] : '';
						$tempArr['OldValue'] = (isset($rowFetch['oldValue']) && empty($rowFetch['oldValue']) == false) ? $rowFetch['oldValue'] : '';
						$tempArr['NewVal'] = (isset($rowFetch['newValue']) && empty($rowFetch['newValue']) == false) ? $rowFetch['newValue'] : '';
						$tempArr['Action'] = (isset($rowFetch['Action']) && empty($rowFetch['Action']) == false) ? $rowFetch['Action'] : '';
						//$tempArr['DBField'] = (isset($rowFetch['dbField']) && empty($rowFetch['dbField']) == false) ? $rowFetch['dbField'] : '';
						$strTime = (isset($rowFetch['AuditDate']) && empty($rowFetch['AuditDate']) == false && $this->validateDate($rowFetch['AuditDate'])) ? strtotime($rowFetch['AuditDate']) : '';
						
						if(empty($strTime) == false){
							$tempArr['Date'] = date('Y-m-d', $strTime);
							$auditArr = &$tmpValArr[$strTime][strtolower($rowFetch['Action'])][$rowFetch['dbField']];
							if(!is_array($auditArr) || !isset($auditArr)) $auditArr = array();
							$auditArr = $tempArr;
						}
					}
				}
			}
			
			if(count($tmpValArr) > 0) $valArr = reset($tmpValArr);
			
			if(count($valArr) > 0){
				$addArr = (isset($valArr['add']) && is_array($valArr['add'])) ? $valArr['add'] : '';
				$updateArr = (isset($valArr['update']) && is_array($valArr['update'])) ? $valArr['update'] : '';
				
				$finalArr = array();
				if(empty($addArr) == false && empty($updateArr) == false) $finalArr = array_merge($addArr, $updateArr);
				
				if(count($finalArr) > 0){
					switch(strtolower($callFrom)){
						case 'ocular':
							if(isset($finalArr['any_conditions_you']['OldValue'])){
								$lastChar = substr($finalArr['any_conditions_you']['OldValue'], -1);
								if($lastChar !== '~|~') $finalArr['any_conditions_you']['OldValue'] = $finalArr['any_conditions_you']['OldValue'].'~|~';
							}
							
							if(isset($finalArr['any_conditions_you']['NewValue'])){
								$lastChar = substr($finalArr['any_conditions_you']['NewValue'], -1);
								if($lastChar !== '~|~') $finalArr['any_conditions_you']['NewValue'] = $finalArr['any_conditions_you']['NewValue'].'~|~';
							}
							
							//Get Patient Other Field value
							if(isset($finalArr['any_conditions_others_you']) && $finalArr['any_conditions_others_you']['OldValue'] == 1 && isset($finalArr['any_conditions_you']) && empty($finalArr['any_conditions_you']) == false){
								$finalArr['any_conditions_you']['OldValue'] = '0'.$finalArr['any_conditions_you']['OldValue'];
								unset($finalArr['any_conditions_others_you']);
							}
							
							//Get Relative Other Field value
							if(isset($finalArr['any_conditions_other_relative']) || $finalArr['any_conditions_other_relative']['OldValue'] == 1 && isset($finalArr['any_conditions_relative']) && empty($finalArr['any_conditions_relative']) == false){
								$finalArr['any_conditions_relative']['OldValue'] = '0'.$finalArr['any_conditions_relative']['OldValue'];
								unset($finalArr['any_conditions_other_relative']);
							}
							
							$ocuDataArr = array();
							$ocuDataArr['PatientId'] = $patientId;
							$ocuDataArr['PtCondition'] = $finalArr['any_conditions_you']['OldValue'].$finalArr['any_conditions_relative']['OldValue'];
							$ocuDataArr['ChronicDescriptions'] = $finalArr['chronicDesc']['OldValue'];
							$ocuDataArr['chronicRelatives'] = $finalArr['chronicRelative']['OldValue'];
							
							$firstKetArr = reset($finalArr);
							$ocuDataArr['ModifiedDate'] = $firstKetArr['Date'];
							$ocuDataArr['OcularId'] = $firstKetArr['TableID'];
							
							$finalOcuData = $this->getOcularData($ocuDataArr);
						break;
					}
				}
			}
		}
		
		return $finalOcuData;
	}
	
	function validateDate($date, $format = 'Y-m-d H:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
	}
}