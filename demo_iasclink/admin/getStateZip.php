<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once("../common/conDb.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$value = $_REQUEST['zip'];
if($_REQUEST['zip']) {
	$details = $objManageData->getRowRecord('zip_codes', 'zip_code', $value);
	if($details){
		$city = $details->city;
		$state_abb = $details->state_abb;
		$cityState = $city.','.$state_abb;
		echo $cityState;
	}
}	
?>