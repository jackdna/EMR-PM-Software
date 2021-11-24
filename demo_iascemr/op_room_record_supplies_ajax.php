<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
$prev_serial_num= trim($_REQUEST["prev_serial_num"]);
$serial_num 	= trim($_REQUEST["serial_num"]);
$invalidMsg = "Invalid Serial Number <br>".$serial_num;
$qry = "SELECT item_id FROM predefine_suppliesused_item_detail WHERE TRIM(serial_number) ='".$serial_num."' AND serial_number!='' AND used_status = '0'";
$res = imw_query($qry) or die(imw_error()); 
if(imw_num_rows($res)>0 || ($prev_serial_num && ($prev_serial_num==$serial_num))) {
	echo '1';	
}else {
	echo $invalidMsg;
	exit();
}
?>