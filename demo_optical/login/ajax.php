<?php 

require_once(dirname(__FILE__)."/../config/config.php");

if($_REQUEST['wn_height'])
{
	$_SESSION['wn_height']=$_REQUEST['wn_height'];
	echo $_REQUEST['wn_height'];
}

?>