<?php
include_once('../../../../config/globals.php');
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/allergies.class.php");
$medical = new MedicalHistory($_REQUEST['showpage']);
$allergies_obj = new Allergies($medical->current_tab);

//--- Get Typeahead Modal ---
if(!empty($_REQUEST['get_allergies_modal'])){
	//making review in database - end
	$update_status = $allergies_obj->get_allergies_modal($_REQUEST);
}
//--- Get Typeahead Modal ---

if(isset($_REQUEST['allergy_modify']) && isset($_REQUEST['del_id'])){
	$return_arr = $allergies_obj->modify_allergies($_REQUEST);
	echo $return_arr;
	exit();
}

if(isset($_REQUEST['save_data'])){
	$return_arr = $allergies_obj->modify_allergies($_REQUEST);
	echo json_encode($return_arr);
	exit();
}

//Get XML typeahead array
if(isset($_REQUEST['get_xml_arr'])){
	$typeahead_arr = $allergies_obj->get_xml_typeahead_arr('yes');
	echo json_encode( str_replace(array('\'','"'),array('',''),$typeahead_arr));
	exit();
}

//Delete Allergies
if(isset($_REQUEST['delete_allergy']) && $_REQUEST['delete_allergy'] == 'yes'){
	$del_status = $allergies_obj->delete_allergies($_REQUEST);
	echo $del_status;
	exit();
}

//Get SNOMED CODE
if(isset($_REQUEST['CxC']) && $_REQUEST['CxC'] == '1'){
	$name= trim($_REQUEST['name']);
	$name = xss_rem($name);
	if( $name ) 
	{
		$code = $allergies_obj->get_snomed($name);
		echo $code;
	}
	exit();
}

?>