<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
$removeBooking = $_REQUEST['removeBooking'];
$selDos = $_REQUEST['selDos'];
$multiPatientInWaitingId = $_REQUEST['multiPatientInWaitingId'];

if($removeBooking=='yes' && $multiPatientInWaitingId) {
	$delBookingQry	= "DELETE FROM patient_in_waiting_tbl WHERE patient_in_waiting_id in(".$multiPatientInWaitingId.")";	
	$delBookingRes  = imw_query($delBookingQry) or die(imw_error());
}
?>