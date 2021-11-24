<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$errors = array();

/*Backup Lens Seg Type Table*/
$sql_table_create = 'CREATE TABLE `in_lens_type_bk_prac_codes` LIKE `in_lens_type`';

if(imw_query($sql_table_create)){
	
	/*Copy Records from live table for backup*/
	$sql_copy_records = 'INSERT INTO `in_lens_type_bk_prac_codes` SELECT * FROM `in_lens_type`';
	if(imw_query($sql_copy_records)){
		
		/*Unset Prac Codes for Seg Type option from DB table as they are not directly editable from the Software FrontEnd*/
		$sql_unset_prac_codes = "UPDATE `in_lens_type` SET `prac_code`=0 WHERE `del_status`=0 AND `vw_code`!=''";
		imw_query($sql_unset_prac_codes) or $errors[] = imw_error();
	}
	else
		$errors[] = imw_error();	
}
else
	$errors[] = imw_error();


if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 113 run successfully...</div>';	
}
?>

