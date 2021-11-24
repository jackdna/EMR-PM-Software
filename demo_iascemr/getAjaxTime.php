<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$settings = $objManageData->loadSettings('vital_time_slot');

$v 		= trim(urldecode($_POST['v']));
//$time	=	date('h:i A', time());
$time	=	$objManageData->getTmFormat(date('h:i A', time()));
if( !empty($v) && $settings['vital_time_slot'] > 0 )
{
	$interval = '+'.$settings['vital_time_slot'].' mins';
	//$time	=	date('h:i A', strtotime($v.$interval));
	$time	=	$objManageData->getTmFormat($v.$interval);
}
echo json_encode($time);

?>
