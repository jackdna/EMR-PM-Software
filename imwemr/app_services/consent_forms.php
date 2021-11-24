<?php 
require_once "inc_classes/consent_forms.php";
$authId = $_REQUEST['phyId'];
$serviceObj = new consent_forms($_REQUEST['patId']);
$serviceObj->reqModule = $reqModule;
$servicesArr = explode(",",$_REQUEST["service"]);
foreach($servicesArr as $key=>$service){
	if(method_exists($serviceObj, trim($service))){
		$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
	}
	else{
		$responseArray[$service] = "NO SCHEDULER SERVICE EXISTS";	
	}	
}
if($_REQUEST['pre'])
pre($responseArray);
echo json_encode($responseArray);	
die();
	

?>