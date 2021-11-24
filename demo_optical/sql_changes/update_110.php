<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();

$sql[] = "CREATE TABLE `in_lens_type_vcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lens_type_id` int(11) NOT NULL,
  `lens_type` varchar(255) NOT NULL,
  `prac_id` int(11) NOT NULL,
  `sph_plus_from` varchar(255) NOT NULL,
  `sph_plus_to` varchar(255) NOT NULL,
  `sph_min_from` varchar(255) NOT NULL,
  `sph_min_to` varchar(255) NOT NULL,
  `cyl_from` varchar(255) NOT NULL,
  `cyl_to` varchar(255) NOT NULL,
  `del_status` tinyint(1) NOT NULL,
  `entered_date` date NOT NULL,
  `entered_time` time NOT NULL,
  `entered_by` int(11) NOT NULL,
  `modified_date` date NOT NULL,
  `modified_time` time NOT NULL,
  `modified_by` int(11) NOT NULL,
  `del_date` date NOT NULL,
  `del_time` time NOT NULL,
  `del_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;";

$sql[]="ALTER TABLE `in_lens_type_vcode` ADD `prac_code` VARCHAR( 250 ) NOT NULL AFTER `lens_type`";
$sql[]="ALTER TABLE `in_lens_type_vcode` ADD `wholesale_price` DOUBLE( 12, 2 ) NOT NULL ,ADD `purchase_price` DOUBLE( 12, 2 ) NOT NULL ,ADD `retail_price` DOUBLE( 12, 2 ) NOT NULL ";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 110 run successfully...</div>';	
}
?>

