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

$patient_id = $_GET['patient_id'];
$usrId = $_GET['usrId'];

if($patient_id) {
	$updateAlrtQry="UPDATE iolink_patient_alert_tbl SET alert_disabled = 'yes',alert_disabled_date_time = '".date('Y-m-d H:i:s')."', alert_disabled_by = '".$usrId."', disabled_section='chart popup' WHERE patient_id  = '".$patient_id."'";
	$updateAlrtRes = imw_query($updateAlrtQry);			
}
?>