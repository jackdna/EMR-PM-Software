<?php 
require_once "inc_classes/consult_letters.php";
$authId = $_REQUEST['phyId'];
$serviceObj = new consult_letters($_REQUEST['patId']);
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