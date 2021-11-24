<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
if($_POST['remove_session']=='yes') {
	$_SESSION['IPadImage'] = NULL;
	$_SESSION['IPadImage'] = "";
	unset($_SESSION['IPadImage']);
}else {
	$picName = $_SESSION['IPadImage'];
	echo $picName;
}
?> 