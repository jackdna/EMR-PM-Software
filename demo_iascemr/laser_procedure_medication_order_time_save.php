<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$patient_id = $_SESSION['patient_id'];
$ascId = $_SESSION['ascId'];
$pConfId = $_SESSION['pConfId'];
$timeVal = $_GET['timeVal'];
imw_query("update laser_procedure_patient_table  set medicationStartTime = '$timeVal'
		where patient_id = '$patient_id' and confirmation_id = '$pConfId'");
?>