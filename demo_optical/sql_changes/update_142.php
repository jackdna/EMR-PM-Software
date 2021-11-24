<?php 
/*
there indexs are created to improve speed of frame data import
changes are proposed by shipa and confirmed by pankaj
*/	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[]="ALTER TABLE  `in_reason` ADD  `not_editable` TINYINT( 1 ) NOT NULL";
$sql[]="update `in_reason` set `not_editable`=1 WHERE id in (11,12)";
$sql[]="ALTER TABLE  `in_stock_detail` ADD  `source` VARCHAR( 50 ) NOT NULL";

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
    echo '<div style="color:green;"><br><br>Update 142 run successfully</div>';
}

?>
