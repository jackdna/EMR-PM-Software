<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$qry = "SHOW INDEX FROM in_batch_records WHERE key_name = 'batch_item_id'";
$res= imw_query($qry) or $errors[] =  imw_error();
if( imw_num_rows($res)==0){
	$qry = "ALTER TABLE  `in_batch_records` ADD INDEX  `batch_item_id` (  `in_item_id` ,  `in_batch_id` )";
	 imw_query($qry) or $errors[] =  imw_error();
	$created=true;
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 133 run successfully. ';
	echo($created==true)?'`<strong>batch_item_id</strong>` index created':'`<strong>batch_item_id</strong>` index already exist';
	echo'</div>';
}

?>