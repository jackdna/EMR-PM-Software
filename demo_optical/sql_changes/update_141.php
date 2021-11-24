<?php 
/*
there indexs are created to improve speed of frame data import
changes are proposed by shipa and confirmed by pankaj
*/	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$qry = "SHOW INDEX FROM in_item WHERE key_name = 'initem_upccode'";
$res= imw_query($qry) or $errors[] =  imw_error();
if( imw_num_rows($res)==0){
	$sql[]="CREATE INDEX initem_upccode ON in_item (upc_code)";
}

$qry = "SHOW INDEX FROM in_item WHERE key_name = 'initem_StylFrmMasID'";
$res= imw_query($qry) or $errors[] =  imw_error();
if( imw_num_rows($res)==0){
	$sql[]="CREATE INDEX initem_StylFrmMasID On in_item(StyleFramesMasterID)";
}

$qry = "SHOW INDEX FROM in_style_brand WHERE key_name = 'instylebrand_stylebrandid'";
$res= imw_query($qry) or $errors[] =  imw_error();
if( imw_num_rows($res)==0){
	$sql[]="CREATE INDEX  instylebrand_stylebrandid ON in_style_brand(style_id,brand_id)";
}

foreach($sql as $qry)
{
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 141 run successfully</div>';
}

?>
