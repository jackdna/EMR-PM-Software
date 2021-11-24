<?php
//error_reporting(-1);
//ini_set("display_errors",-1);
$ignoreAuth = true;
//$practicePath = 'idoc';
if(isset($argv[1])){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}
set_time_limit(0);
require_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);
if(constant('GENERATE_CUBIXX_CHARGES_XML') && strtolower(constant('GENERATE_CUBIXX_CHARGES_XML'))=='yes'){
	include_once(dirname(__FILE__)."/../../addons/cubixxXML/generate_xml.php");
	$todays_pcl_id_array = array();
	$todays_posted_pcl_id_array	= cubixx_get_todays_posted_charges('2020-05-01');
//	pre($todays_posted_pcl_id_array);
//	echo '<hr>';
	
	$todays_paid_pcl_id_array 	= cubixx_get_todays_paid_charges('2020-05-01');
//	pre($todays_paid_pcl_id_array);
//	echo '<hr>';
	if($todays_posted_pcl_id_array && is_array($todays_posted_pcl_id_array)){
		$todays_pcl_id_array = $todays_posted_pcl_id_array;
	}
	if($todays_paid_pcl_id_array && is_array($todays_paid_pcl_id_array)){
		$todays_pcl_id_array = array_merge($todays_pcl_id_array,$todays_paid_pcl_id_array);
	}
	
	if(is_array($todays_pcl_id_array) && count($todays_pcl_id_array)>0){
		$todays_pcl_id_array = array_unique($todays_pcl_id_array);
//		pre($todays_pcl_id_array);
		foreach($todays_pcl_id_array as $charge_list_id){
		//	echo  $charge_list_id.',';
			cubixx_create_charges_xml($charge_list_id);
		}
		die('process complete.');
	}else{
		die("No record found.");
	}
}


?>