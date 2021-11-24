<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql	= $sql1		= array();
$error	= $error1	= array();

$sql[]	= "ALTER TABLE `in_order` ADD `tax_pt_paid` DOUBLE(12, 2) NOT NULL DEFAULT 0.00 AFTER `tax_payable`";
$sql[]	= "ALTER TABLE `in_order` ADD `tax_custom` BOOLEAN NOT NULL DEFAULT 0 AFTER `tax_pt_paid`";
$sql[]	= "ALTER TABLE `in_order_details` ADD `color_code` VARCHAR(50) NOT NULL AFTER `color_id`";
$sql[]	= "ALTER TABLE `in_order_details` ADD `shape_other` VARCHAR(50) NOT NULL AFTER `style_other`";
$sql[]	= "ALTER TABLE `in_order_details` ADD `color_other` VARCHAR(50) NOT NULL AFTER `color_id`";
$sql[]	= "ALTER TABLE `in_prac_codes` CHANGE `prac_code` `prac_code` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;
";

/*Multiple Prac codes based on Seg Type for Lens Material*/
$sql[]	= "ALTER TABLE `in_lens_material` CHANGE `prac_code` `prac_code_sv` VARCHAR(255) NOT NULL COMMENT 'Seg Type'";

/*Prac Codes (Additional columns) */
$sql1[]	= "ALTER TABLE `in_lens_material` ADD `prac_code_pr` VARCHAR(255) NOT NULL COMMENT 'Progressive' AFTER `prac_code_sv`";
$sql1[]	= "ALTER TABLE `in_lens_material` ADD `prac_code_bf` VARCHAR(255) NOT NULL COMMENT 'BiFocal' AFTER `prac_code_pr`";
$sql1[]	= "ALTER TABLE `in_lens_material` ADD `prac_code_tf` VARCHAR(255) NOT NULL COMMENT 'TriFocal' AFTER `prac_code_bf`";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

foreach($sql1 as $qry1){
	imw_query($qry1) or $error1[] = imw_error();
}

/*Copy Prac. codes in additional columns*/
if(count($error1)==0){
	$copy_data = "UPDATE `in_lens_material` SET `prac_code_pr`=`prac_code_sv`, `prac_code_bf`=`prac_code_sv`, `prac_code_tf`=`prac_code_sv`";
	imw_query($copy_data);
}


if(count($error)>0 || count($error1)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $error);
	print "\n".implode("\n", $error1);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 108 run successfully...</div>';	
}
?>