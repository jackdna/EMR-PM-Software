<?php
require_once("../config/config.php");
require_once("classes/common_functions.php"); 
if($_REQUEST['action']!="" && $_REQUEST['action']=="ChangeFacility")
{
	$_SESSION["pro_fac_id"] = $_REQUEST['fac'];
	echo $_SESSION["pro_fac_id"];
}

?>