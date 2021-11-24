<?php 
/*
 * Purpose: Remove leading and trailing Quotes from frame Color Name
*/

$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();
$count=0;

$sql="SHOW INDEX FROM in_item WHERE key_name = 'initem_upccode' ";
$row=imw_query($sql);
if(!imw_num_rows($row)){
	imw_query("CREATE INDEX initem_upccode ON in_item (upc_code)")or $msg_info[] = imw_error();
	$count++;
}

$sql1="SHOW INDEX FROM in_item WHERE key_name = 'initem_StylFrmMasID' ";
$row1=imw_query($sql1);
if(!imw_num_rows($row1)){
	imw_query("CREATE INDEX initem_StylFrmMasID On in_item(StyleFramesMasterID)")or $msg_info[] = imw_error();
	$count++;
}



echo '<div><br><br>'.$count.' Indexes created successfully.</div>';