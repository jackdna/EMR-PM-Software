<?php 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
$ignoreAuth = true;
include("../../../config/globals.php");
require_once(dirname(__FILE__) . "/../../../library/classes/audit_common_function.php");
$printOption = $_REQUEST['print_op'];
$printOption = explode(",", $printOption);

$plPHI = 0;
$plPHI = (int) $_SESSION['AUDIT_POLICIES']['PHI_Export'];
/*
  $qryGetAuditPolicies = "select policy_status as plPHI from audit_policies where policy_id = 11";
  $rsGetAuditPolicies = imw_query($qryGetAuditPolicies);
  if($rsGetAuditPolicies){
  if(imw_num_rows($rsGetAuditPolicies)){
  extract(imw_fetch_array($rsGetAuditPolicies));
  }
  }
  else{
  $phiError = "Error : ". imw_errno() . ": " . imw_error();
  } */

$arrAuditTrailPHI = array();
if ($plPHI == 1) {
    $opreaterId = $_SESSION['authId'];
    $ip = getRealIpAddr();
    $URL = $_SERVER['PHP_SELF'];
    $os = getOS();
    $browserInfoArr = array();
    $browserInfoArr = _browser();
    $browserInfo = $browserInfoArr['browser'] . "-" . $browserInfoArr['version'];
    $browserName = str_replace(";", "", $browserInfo);
    $machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    foreach ($printOption as $val) {
        if ($_SESSION['authId']) {
            $val = preg_replace('#\d+#', '', $val);
            $val = str_replace(".php", "", $val);
            if ($val) {
                $arrAuditTrailPHI [] = array(
                            "Pk_Id" => $_SESSION['patient'],
                            "Table_Name" => "patient_data",
                            "Action" => "phi_export",
                            "Operater_Id" => $opreaterId,
                            "Operater_Type" => get_operator_type($opreaterId),
                            "IP" => $ip,
                            "MAC_Address" => $_REQUEST['macaddrs'],
                            "URL" => $URL,
                            "Browser_Type" => $browserName,
                            "OS" => $os,
                            "Machine_Name" => $machineName,
                            "pid" => $_SESSION['patient'],
                            "Category" => "chart_notes-patient_information",
                            "Category_Desc" => $val,
                            "Old_Value" => $_SESSION['patient'],
                            "Depend_Select" => "select CONCAT(CONCAT_WS(', ',lname,fname),'(',id,')') as patientName",
                            "Depend_Table" => "patient_data",
                            "Depend_Search" => "id",
                            "New_Value" => $_SESSION['form_id']
                );
            }
        }
    }
    $table = array("audit_policies");
    $error = array($phiError);
    $mergedArray = merging_array($table, $error);
    auditTrail($arrAuditTrailPHI, $mergedArray, 0, 0, 0);
}
echo "DONE";
?>