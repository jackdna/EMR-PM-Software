<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="INSERT INTO `in_prac_codes` (`id`, `module_id`, `sub_module`, `prac_code`) VALUES ('14', '2', 'uv400', '')";
/*Fix for already having wrong values*/
$sql[]="UPDATE `in_prac_codes` SET `sub_module`='uv400', `module_id`=2 WHERE `id`=14 AND `module_id`=3 LIMIT 1";
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
	echo '<div style="color:green;"><br><br>Update 80 run successfully...</div>';	
}

?>