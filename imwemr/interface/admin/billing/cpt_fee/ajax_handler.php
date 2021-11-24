<?php 
	require_once("../../../../config/globals.php");
	require_once("../../../../library/classes/common_function.php");
	require_once("../../../../library/classes/cpt_fee_class.php");

	$operator_id = $_SESSION['authUserID'];
	$entered_date = date('Y-m-d H:i:s'); 

	$cpt_fee_obj = New CPT_Fee($operator_id);
	
	if(isset($_REQUEST['ajax_request'])){
		if(isset($_REQUEST['saveDataFld'])){
			$save_status = $cpt_fee_obj->save_header_data($_REQUEST);
			echo $save_status;
		}
		if(isset($_REQUEST['inc_dec_save'])){
			$save_status = $cpt_fee_obj->save_header_data($_REQUEST);
			echo $save_status;
		}
		if(isset($_REQUEST['saveData'])){
			$save_status = $cpt_fee_obj->save_fee_table_data($_REQUEST);
			echo $save_status;
		}
		if(isset($_REQUEST['DelColumn'])){
			$save_status = $cpt_fee_obj->del_fee_col($_REQUEST);
			echo $save_status;
		}
		die();
	}
	
	
?>