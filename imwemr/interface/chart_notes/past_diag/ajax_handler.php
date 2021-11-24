<?php
require_once(dirname(__FILE__).'/../../../config/globals.php');

$library_path = $GLOBALS['webroot'].'/library';
include_once $GLOBALS['srcdir']."/classes/pt_at_glance.class.php";

if(isset($_REQUEST["p_id"]) && !empty($_REQUEST["p_id"])){
	$pid = $_REQUEST["p_id"];
}else{
	$pid = $_SESSION["patient"];
}
$authUser=$_SESSION['authUser'];

//Pt glance obj.
$pt_glance = New Pt_at_glance($pid,$authUser,$_REQUEST);

if(isset($_REQUEST['ajax_request']) && empty($_REQUEST['ajax_request']) === false){
	//Updates record limits to show
	if(isset($_REQUEST['elem_formAction']) && $_REQUEST['elem_formAction'] == 'set_pag_records_lmt'){
		$v=$_GET["set_shw_rec"];			
		$pt_glance->usrChartLimit_Pag(2,$v);
	}
	
	//Get Graph array
	if(isset($_REQUEST['get_graph_data']) && empty($_REQUEST['get_graph_data']) === false){
		include_once $GLOBALS['srcdir']."/classes/work_view/PtIop.php";
		include_once $GLOBALS['srcdir']."/classes/work_view/wv_functions.php";
		//$graph_data = $pt_glance->get_graph_data($_REQUEST);
		//echo $graph_data;
		$oic = new PtIop($pid);
		$oic->getGraph();
	}
	
	//Save pt glance information
	if(isset($_REQUEST['save_information']) && empty($_REQUEST['save_information']) === false){
		if(isset($_REQUEST['save_target'])){
			$save_status = $pt_glance->save_chart_values($_REQUEST);
		}else{
			$save_status = $pt_glance->save_chart_comments($_REQUEST);
		}
		
		echo $save_status;
	}
	die();
}


?>