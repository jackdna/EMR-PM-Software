<?php
include_once('../../../../config/globals.php');
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/vital_signs.class.php");
$medical = new MedicalHistory($_REQUEST['showpage']);
$vs_obj = new Vital_Sign($medical->current_tab);

 
//--- DELETE VITAL SIGN DATA ----
if(empty($mode) == false and empty($del_id) == false){
	$del_status = $vs_obj->delete_vital_sign($_REQUEST);
	echo $del_status;
	exit();
}

// -- Get graph data
if(isset($_REQUEST['get_graph']) && $_REQUEST['get_graph'] == 'yes'){
	$graph_data = $vs_obj->get_graph_data($_REQUEST);
	echo json_encode($graph_data);
	exit();
}

?>