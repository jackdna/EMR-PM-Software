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

include_once(dirname(__FILE__)."/../../globals.php");

$opreaterId = $_SESSION['authId'];	
$ip = getRealIpAddr();
$URL = $_SERVER['PHP_SELF'];													 
$os = getOS();
$browserInfoArr = array();
$browserInfoArr = _browser();
$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
$browserName = str_replace(";","",$browserInfo);													 
$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

$arrAuditTrailInsCase = array();
$arrAuditTrailInsCase [] = array(		
								"Pk_Id"=> $_SESSION['currentCaseid'],
								"Table_Name"=>"insurance_case",								
								"Data_Base_Field_Name"=> "ins_case_type" ,
								"Filed_Label"=> "inscasetype",
								"Filed_Text"=> "Patient Insurance Case Type",													
								"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_case_type"),				
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"Machine_Name"=> $machineName,
								"Category"=> "patient_info",
								"Category_Desc"=> "insurence",	
								"Old_Value"=> $caseType,			
								"Action"=> "update",
								"pid"=> $_SESSION['patient']
							);
$arrAuditTrailInsCase [] = array(											
								"Table_Name"=>"insurance_case",								
								"Data_Base_Field_Name"=> "ins_caseid" ,
								"Filed_Label"=> "case_id",
								"Filed_Text"=> "Patient Insurance Case id",
								"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_caseid") ,											
								"Old_Value"=> $Caseid,
								"Action"=> "update"																																					
							);
$arrAuditTrailInsCase [] = array(
								"Table_Name"=>"insurance_case",								
								"Data_Base_Field_Name"=> "start_date" ,
								"Filed_Label"=> "case_startdate",
								"Filed_Text"=> "Patient Insurance Case Start Date",
								"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"start_date"),
								"Old_Value"=> $start_date,		
								"Action"=> "update"	
							);
$arrAuditTrailInsCase [] = array(		
								"Table_Name"=>"insurance_case",								
								"Data_Base_Field_Name"=> "end_date" ,
								"Filed_Label"=> "case_enddate",
								"Filed_Text"=> "Patient Insurance Case End Date",
								"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"end_date") ,
								"Old_Value"=> $end_date,		
								"Action"=> "update"				
							);
$arrAuditTrailInsCase [] = array(		
								"Table_Name"=>"insurance_case",								
								"Data_Base_Field_Name"=> "case_status" ,
								"Filed_Label"=> "case_status",
								"Filed_Text"=> "Patient Insurance Case Status",
								"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"case_status"),
								"Old_Value"=> $case_status,		
								"Action"=> "update"																		
							);	
$serialized = serialize($arrAuditTrailInsCase);
$audit_cases_str = urlencode($serialized);

$arrAuditTrailView = array();
$arrTable = array();

$arrTable = makeUnique(auditViewArray($arrAuditTrailInsCase));
if(sizeof($arrTable)>1){
	foreach ($arrTable as $key => $value) {
		$arrAuditTrailView [] = array(
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
									"Category"=> "patient_info",
									"Filed_Label"=> "Patient Ins. Case Data",
									"Category_Desc"=> "insurance"					
								);
	}					
}
$table = array("insurance_case");
$error = array($insError);
$mergedArray = merging_array($table,$error);
$patientViewed = array();
if(isset($_SESSION['Patient_Viewed'])){
	$patientViewed = $_SESSION['Patient_Viewed'];	
	if($patientViewed["Insurance"] == 0){
		if($data['policy_status'] == 1){
			auditTrail($arrAuditTrailView,$mergedArray,$_SESSION['currentCaseid'],0,0);
		}
	}
}
?>