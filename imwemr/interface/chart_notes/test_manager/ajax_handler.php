<?php
	require_once("../../../config/globals.php");

	//To check pt logged in or not
	require_once("../../../library/patient_must_loaded.php");

	$library_path = $GLOBALS['webroot'].'/library';

	include_once($GLOBALS['srcdir'].'/classes/common_function.php');
	include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
	include_once($GLOBALS['srcdir'].'/classes/SaveFile.php');
	include_once($GLOBALS['srcdir'].'/classes/pt_at_glance.class.php');
	
	$pt_glance_obj = New Pt_at_glance;
	
	if(isset($_REQUEST['task'])){
		$s	= (isset($_REQUEST['setiname']) && trim($_REQUEST['setiname'])!='')		?	trim($_REQUEST['setiname'])	: '';					//setting variable name.
		$v	= (isset($_REQUEST['setival']) && trim($_REQUEST['setival'])!='')		?	trim($_REQUEST['setival'])	: '';					//setting variable value.
		$t	= (isset($_REQUEST['task']) && trim($_REQUEST['task'])!='')				?	trim($_REQUEST['task'])		: ''; 					//action to perform.
		$u	= (isset($_REQUEST['userid']) && trim($_REQUEST['userid'])!='')			?	trim($_REQUEST['userid'])	: $_SESSION['authId'];	//user ID, whose settings.
		switch($t){
			case 'getSetting':
				$settings = $pt_glance_obj->getUserSettings($u,$s);
				if(!is_array($settings)) echo $settings;
				else echo '';
				break;
			case 'setSetting':
				$settings = $pt_glance_obj->setUserSettings($u,$s,$v);
				echo $settings; //"true" or mysql_error()'
				break;
			default: //do nothitng.
				break;
		}
	}
?>	