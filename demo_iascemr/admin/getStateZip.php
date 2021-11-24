<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$value = $_REQUEST['zip'];
$details = $objManageData->getRowRecord('zip_codes', 'zip_code', $value);
if($details){
	$city = $details->city;
	$state = $details->state_abb;
	$cityState = $city.','.$state;
	echo $cityState;
}
?>