<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[] = "CREATE TABLE `in_batch_table` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `save_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

$sql[] = "CREATE TABLE `in_batch_records` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_upc_code` varchar(20) NOT NULL,
  `in_item_id` int(11) NOT NULL,
  `in_item_quant` int(10) NOT NULL,
  `in_batch_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

$sql[]="CREATE TABLE `in_log_quant_edit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `upc_code` varchar(255) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `changed_date` date NOT NULL,
  `changed_time` time NOT NULL,
  `existing_quant` int(11) NOT NULL,
  `updated_quant` int(11) NOT NULL,
  `action` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

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
	echo '<div style="color:green;"><br><br>Update 52 run successfully...</div>';	
}

?>