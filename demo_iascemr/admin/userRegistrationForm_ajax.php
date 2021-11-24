<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../common/conDb.php");

$user_id 		= $_POST["user_id"];
$username 		= trim(addslashes($_POST["username"]));
$pass 			= $_POST["pass"];
$npi 			= trim($_POST["npi"]);
$initial 		= trim($_POST["initial"]);
$sso_identifier	= trim(addslashes($_POST["sso_identifier"]));
$andUsrIdQry = "";
if($user_id) { $andUsrIdQry = " AND usersId != '".$user_id."' "; }

$qry = "SELECT usersId,loginName FROM `users` WHERE deleteStatus !='Yes' AND  TRIM(loginName) = '".$username."'".$andUsrIdQry;
$res = imw_query($qry) or die(imw_error()); 
if(imw_num_rows($res)>0) {
	echo "Login Name Already Exist";
	exit();	
}

$qryNpi = "SELECT usersId FROM `users` WHERE deleteStatus !='Yes' AND  TRIM(npi)!='' AND TRIM(npi)!='0' AND TRIM(npi) = '".$npi."'".$andUsrIdQry;
$resNpi = imw_query($qryNpi) or die(imw_error()); 
if(imw_num_rows($resNpi)>0) {
	echo "NPI Already Exist";
	exit();	
}

$qryInitial	= "SELECT usersId FROM `users` WHERE deleteStatus !='Yes' AND  TRIM(initial) <> '' AND TRIM(initial) = '".$initial."'".$andUsrIdQry;
$resInitial = imw_query($qryInitial) or die(imw_error()); 
if(imw_num_rows($resInitial)>0) {
	echo "Initial Already Exist";
	exit();	
}

$qrySSO	= "SELECT usersId FROM `users` WHERE deleteStatus !='Yes' AND  TRIM(sso_identifier) <> '' AND TRIM(sso_identifier) = '".$sso_identifier."'".$andUsrIdQry;
$resSSO = imw_query($qrySSO) or die(imw_error()); 
if(imw_num_rows($resSSO)>0) {
	echo "SSO ID Already Exist";
	exit();	
}

?>
