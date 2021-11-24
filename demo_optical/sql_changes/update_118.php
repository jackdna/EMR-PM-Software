<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();

$count	= false;
/*Check if data already exists*/
$sql	= "SELECT COUNT(`id`) AS cont FROM `in_prac_codes` WHERE `module_id` in(5,6)";
$resp	= imw_query($sql) or $errors[] = imw_error();

if($resp){
	$count = imw_fetch_assoc($resp);
	$cont = $count['cont'];
}

if($cont == 0){

/*Delete default Prac Code for UV400*/
	$sql_uv = "INSERT INTO `in_prac_codes` (`module_id`, `sub_module`, `sub_module_label`, `prac_code`, `del_status`) 
	VALUES ('5', '', '', '', '0'), ('6', '', '', '', '0'), ('7', '', '', '', '0');";
	imw_query($sql_uv) or $errors[] = imw_error();
}
else{
	$errors[] = 'Data already exists.';
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 118 run successfully...</div>';
}

?>