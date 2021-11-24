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
/*	
File: delete_family_info.php
Purpose: Delete patient's family information
Access Type: Direct 
*/
include_once("../../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
//include_once($GLOBALS['srcdir']."/classes/demographics.class.php");
//$patient_data_obj = new Demographics();
//$defaults	=	$patient_data_obj->load_defaults_data();
//$vocabulary = $defaults['vocabulary'];

$demoFamilyInfoDataFields = array(); 
$demoFamilyInfoDataFields = make_field_type_array('patient_family_info');

//--- GET PATIENT FAMILY RELATION ----
$row = get_extract_record('patient_family_info','id',$id, 'patient_relation');
$patientRelation = $row['patient_relation'];

//--- DELETE PATIENT FAMILY INFORMATION -----
$res = imw_query("Delete From patient_family_info Where id = '".$id."'");

//--- AUDIT CODE FOR DELETE ---->
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

if($policyStatus == 1){
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];	 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);										 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	$arrAuditTrail = array();
	$arrAuditTrail[0]["Pk_Id"] = $id;
	$arrAuditTrail[0]["Table_Name"] = "patient_family_info";
	$arrAuditTrail[0]["Data_Base_Field_Name"] = "id";
	$arrAuditTrail[0]["Data_Base_Field_Type"] = fun_get_field_type($demoFamilyInfoDataFields,"id");
	$arrAuditTrail[0]["Filed_Text"] = "Patient Family Information ".$familyNo;
	$arrAuditTrail[0]["Old_Value"] = $patientRelation;
	$arrAuditTrail[0]["Operater_Id"] = $_SESSION['authId'];
	$arrAuditTrail[0]["Operater_Type"] = getOperaterType($_SESSION['authId']);
	$arrAuditTrail[0]["IP"] = $ip;
	$arrAuditTrail[0]["MAC_Address"] = $_REQUEST['macaddrs'];
	$arrAuditTrail[0]["URL"] = $URL;
	$arrAuditTrail[0]["Browser_Type"] = $browserName;
	$arrAuditTrail[0]["OS"] = $os;
	$arrAuditTrail[0]["Machine_Name"] = $machineName;
	$arrAuditTrail[0]["Category"] = "patient_info";
	$arrAuditTrail[0]["Category_Desc"] = "demographics";
	$arrAuditTrail[0]["Action"] = "delete";
	$arrAuditTrail[0]["pid"] = $_SESSION['patient'];
	$arrAuditTrail[0]["Date_Time"] = date('Y-m-d H:i:s');
	
	$table = array("lists");
	$error = array($demoFamilyInfoDataFields);
	$mergedArray = mergingArray($table,$error);			

	auditTrail($arrAuditTrail,$mergedArray,$id,0,0);
}


$return = array('success' => false, 'msg_key' => ''); 
if($res) 
{
	$return = array('success' => true, 'msg_key' => 'save_family_info');
}

echo json_encode($return);
?>
