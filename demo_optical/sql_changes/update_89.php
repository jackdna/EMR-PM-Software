<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="UPDATE  `in_prac_codes` SET  `sub_module` =  'coating' WHERE  `in_prac_codes`.`id` =4 LIMIT 1 ;";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 89 run successfully...</div>';	
}

?>