<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();
$q1=imw_query("select * from in_reason where reason_name='Reset on reconcile'");
if(imw_num_rows($q1)==0){
$sql[] = "INSERT INTO `in_reason` (`id`, `reason_name`, `del_status`, `entered_date`, `entered_time`, `entered_by`, `modified_date`, `modified_time`, `modified_by`, `del_date`, `del_time`, `del_by`) VALUES (NULL, 'Reset on reconcile', '', '', '', '1', '', '', '', '', '', '');";
}
$q2=imw_query("select * from in_reason where reason_name='Manual Reset'");
if(imw_num_rows($q2)==0){
$sql[] = "INSERT INTO `in_reason` (`id`, `reason_name`, `del_status`, `entered_date`, `entered_time`, `entered_by`, `modified_date`, `modified_time`, `modified_by`, `del_date`, `del_time`, `del_by`) VALUES (NULL, 'Manual Reset', '', '', '', '1', '', '', '', '', '', '');";
}
//$sql[] = "ALTER TABLE  `in_item_lot_total` ADD  `retail_price` DOUBLE(12, 2) NOT NULL AFTER `wholesale_price`";

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
    echo '<div style="color:green;"><br><br>Update 138 run successfully</div>';
}

?>
