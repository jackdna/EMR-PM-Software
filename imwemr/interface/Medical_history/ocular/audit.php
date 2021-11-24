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
?>
<?php
	require_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
	
	$ocularDataFields = array(); 
	$ocularDataFields = make_field_type_array("ocular");
	if($ocularDataFields == 1146){
		$oculerError = "Error : Table 'ocular' doesn't exist";
	}
	
	$arrAuditTrail_Ocular = array();
	$opreaterId = $_SESSION["authId"];												 
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	$arrAuditTrail_Ocular [] = array(
						"Pk_Id"=> $result["ocular_id"],
						"Table_Name"=> "ocular",
						"Data_Base_Field_Name"=> "you_wear" ,
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"you_wear") ,
						"Filed_Label"=> "u_wear",
						"Filed_Text"=> "Do you wear",
						"Action"=> "update",
						"Operater_Id"=> $opreaterId,
						"Operater_Type"=> getOperaterType($opreaterId) ,
						"IP"=> $ip,
						"MAC_Address"=> $_REQUEST['macaddrs'],
						"URL"=> $URL,
						"Browser_Type"=> $browserName,
						"OS"=> $os,
						"Machine_Name"=> $machineName,
						"Category"=> "patient_info-medical_history",
						"Category_Desc"=> "ocular",	
						"Old_Value"=> $result["you_wear"]
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "last_exam_date" ,
						"Filed_Label"=> "exam_date",
						"Filed_Text"=> "Last eye exam date",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"last_exam_date") ,
						"Old_Value"=> addcslashes(addslashes($result['last_exam_date']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "eye_problems" ,
						"Filed_Label"=> "eye_problem",
						"Filed_Text"=> "Eye Problems",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"eye_problems") ,
						"Old_Value"=> addcslashes(addslashes($result['eye_problems']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "eye_problems_other" ,
						"Filed_Label"=> "eye_problem_other",
						"Filed_Text"=> "Eye Problems Others",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"eye_problems_other") ,
						"Old_Value"=> addcslashes(addslashes($result['eye_problems_other']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "any_conditions_you" ,
						"Filed_Label"=> "any_conditions_u",
						"Filed_Text"=> "Any condition you have presently or have had in the past",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"any_conditions_you") ,
						"Old_Value"=> addcslashes(addslashes($result['any_conditions_you']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "any_conditions_relative" ,
						"Filed_Label"=> "any_conditions_relative",
						"Filed_Text"=> "Any condition blood relative have presently or have had in the past",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"any_conditions_relative") ,
						"Old_Value"=> addcslashes(addslashes($result['any_conditions_relative']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "any_conditions_others_you" ,
						"Filed_Label"=> "any_conditions_other_u",
						"Filed_Text"=> "Any other condition you have presently or have had in the past",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"any_conditions_others_you") ,
						"Old_Value"=> ($result['any_conditions_others_you']) ? $result['any_conditions_others_you'] : ""
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "any_conditions_other_relative" ,
						"Filed_Label"=> "any_conditions_other_relative",
						"Filed_Text"=> "Any other condition blood relative have presently or have had in the past",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"any_conditions_other_relative") ,
						"Old_Value"=> ($result['any_conditions_other_relative']) ? $result['any_conditions_other_relative'] : ""
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "OtherDesc" ,
						"Filed_Label"=> "OtherDesc",
						"Filed_Text"=> "Any other condition have presently or have had in the past",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"OtherDesc") ,
						"Old_Value"=> addcslashes(addslashes($result['OtherDesc']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "chronicDesc" ,
						"Filed_Label"=> "elem_chronicDesc",
						"Filed_Text"=> "Any other condition you",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"chronicDesc") ,
						"Old_Value"=> addcslashes(addslashes($result['chronicDesc']),"\0..\37!@\177..\377")
					);
	$arrAuditTrail_Ocular [] = array(																								
						"Data_Base_Field_Name"=> "chronicRelative" ,
						"Filed_Label"=> "elem_chronicRelative",
						"Filed_Text"=> "Any other condition relative",
						"Data_Base_Field_Type"=> fun_get_field_type($ocularDataFields,"chronicRelative") ,
						"Old_Value"=> addcslashes(addslashes($result['chronicRelative']),"\0..\37!@\177..\377")
					);
	
	// Audit View
	
	$arrAuditTrailView_Ocular = array();
	$arrTable = array();
	$arrTable = makeUnique(auditViewArray($arrAuditTrail_Ocular));
	//echo '<pre>';
	//print_r($arrAuditTrail);
	foreach ($arrTable as $key => $value) {
		if(trim($arrTable [$key]["value"]) == "ocular"){
			if (!array_key_exists('Filed_Label', $arrTable[$key])) {
				$arrTable [$key]["Filed_Label"] = "Patient Ocular Data";
				$arrTable [$key]["Category_Desc"] = "ocular";						
			}
		}
	}
			
	foreach ($arrTable as $key => $value) {
		$arrAuditTrailView_Ocular [] = 
					array(
							"Pk_Id"=> $arrTable [$key]["key"],
							"Table_Name"=> $arrTable [$key]["value"],
							"Action"=> "view",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "patient_info-medical_history",
							"Filed_Label"=> $arrTable [$key]["Filed_Label"],
							"Category_Desc"=> $arrTable [$key]["Category_Desc"]													
						);
	}
	
	$table = array("ocular");
	$error = array($oculerError);
	$mergedArray = merging_array($table,$error);
	$patientViewed = array();
	
	
	
	if(isset($_SESSION['Patient_Viewed'])){
		if(is_array($_SESSION['Patient_Viewed'])){$patientViewed = $_SESSION['Patient_Viewed'];}	
		$patientViewed["Medical History"]["Ocular"];
		if($patientViewed["Medical History"]["Ocular"] == 0){
			if($this->policy_status == 1){
				auditTrail($arrAuditTrailView_Ocular,$mergedArray);
				$patientViewed["Medical History"]["Ocular"] = 1;			
				$_SESSION['Patient_Viewed'] = $patientViewed;
			}	
		}
	}
	//echo"<pre>";
	//print_r($_SESSION['Patient_Viewed']);
	
	//echo "<pre>";
	//print_r($arrAuditTrail_Ocular);


?>