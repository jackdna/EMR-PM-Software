<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("conDb.php");

$chkBxAssist = $_GET['chkBxAssist'];
$pConfId = $_GET['pConfId'];

if($chkBxAssist) {
	$updateChbxAssistByTransQry = "update patientconfirmation set assist_by_translator = '$chkBxAssist' where patientConfirmationId = '$pConfId'";
	$updateChbxAssistByTransRes = imw_query($updateChbxAssistByTransQry);
}
?>