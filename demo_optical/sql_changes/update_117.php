<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();

$count	= false;
/*Check if data already exists*/
$sql	= 'SELECT COUNT(`id`) AS \'count\' FROM `in_prac_codes` WHERE `sub_module` LIKE \'type_%\'';
$resp	= imw_query($sql) or $errors[] = imw_error();

if($resp){
	$count = imw_fetch_assoc($resp);
	$count = $count['count'];
}

if($count == 0){

/*Delete default Prac Code for UV400*/
	$sql_uv = 'UPDATE `in_prac_codes` SET `del_status`=1 WHERE `module_id`=2 AND `sub_module`=\'uv400\' AND `sub_module_label`=\'uv400\'';
	imw_query($sql_uv) or $errors[] = imw_error();

/*Set different row for Design Prac Code*/
	$sql_design = 'UPDATE `in_prac_codes` SET `sub_module`=\'design\', `sub_module_label`=\'Design\' WHERE `module_id`=2 AND `sub_module`=\'type\' AND `sub_module_label`=\'Design/Type\'';
	imw_query($sql_design) or $errors[] = imw_error();

/*Prace codes for Seg Types*/
	$sql_seg_prac_code = 'INSERT INTO `in_prac_codes` (`module_id`, `sub_module`, `sub_module_label`) VALUES
						 (2, \'type_sv\', \'Single Vision\'),
						 (2, \'type_pr\', \'Progressive\'),
						 (2, \'type_bf\', \'BiFocal\'),
						 (2, \'type_tf\', \'TriFocal\')';
	imw_query($sql_seg_prac_code) or $errors[] = imw_error();
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
	echo '<div style="color:green;"><br><br>Update 117 run successfully...</div>';
}

?>