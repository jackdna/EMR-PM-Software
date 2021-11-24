<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

session_start();
include_once("common/conDb.php");
$patient_id = $_REQUEST['patient_id'];
$pConfId 	= $_REQUEST['pConfId'];
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}
if(!$pConfId) {
	$pConfId	= $_SESSION['pConfId'];
}
$timeVal = $_GET['timeVal'];
imw_query("update preopphysicianorders set medicationStartTime = '$timeVal'
		where patient_id = '$patient_id' and patient_confirmation_id = '$pConfId'");
?>