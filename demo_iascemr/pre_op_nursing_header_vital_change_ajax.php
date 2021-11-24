<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
	$chngId=$_GET['chngId']; 

	$SavePreopnursingVitalSignIdQry = "UPDATE `preopnursingrecord` SET  preopnursing_vitalsign_id = '".$chngId."'
										WHERE confirmation_id = '".$_REQUEST['pConfId']."'";
	$SavePreopnursingVitalSignIdRes = imw_query($SavePreopnursingVitalSignIdQry) or die(imw_error());
?>            

