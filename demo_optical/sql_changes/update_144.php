<?php 	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$qry = "SHOW INDEX FROM in_order_lens_price_detail WHERE key_name = 'inorderlenspricedetail_ordercol'";
$res=imw_query($qry) or $errors[] = imw_error();
if(imw_num_rows($res)==0){
	$qry = "CREATE INDEX inorderlenspricedetail_ordercol ON in_order_lens_price_detail(order_id,order_detail_id,order_chld_id)";
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 144: Index created successfully</div>';
}

?>
