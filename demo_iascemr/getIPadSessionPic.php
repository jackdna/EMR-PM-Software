<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
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