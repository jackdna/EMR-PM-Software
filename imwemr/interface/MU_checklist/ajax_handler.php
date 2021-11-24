<?php 
	include_once(dirname(__FILE__)."/../../config/globals.php");
	include_once $GLOBALS['srcdir']."/classes/MUR_class.php";
	
	$pid = $_SESSION['patient'];
	$mur_obj = new MUR($pid);
	
	if(isset($_REQUEST['temp_key_size']) && trim($_REQUEST['temp_key_size']) != ''){
		$temp_key = $mur_obj->get_temp_key($_REQUEST);
		echo $temp_key;
		exit();
	}
	
	if(isset($_REQUEST['save_mu_data']) && trim($_REQUEST['save_mu_data']) != ''){
		$save_status = $mur_obj->save_mu_data($_REQUEST);
		echo $save_status;
		exit();
	}
?>