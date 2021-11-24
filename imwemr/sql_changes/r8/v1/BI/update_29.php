<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("CREATE TABLE IF NOT EXISTS `manual_batch_tx_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `charge_list_id` int(11) NOT NULL,
  `charge_list_detail_id` int(11) NOT NULL,
  `pri_due` double(12,2) NOT NULL,
  `sec_due` double(12,2) NOT NULL,
  `tri_due` double(12,2) NOT NULL,
  `pat_due` double(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `entered_date` datetime NOT NULL COMMENT 'Trans Entered Date time',
  `payment_time` time NOT NULL DEFAULT '00:00:00',
  `operator_id` int(11) NOT NULL,
  `del_status` int(2) NOT NULL,
  `del_operator_id` int(12) NOT NULL COMMENT 'operator deleted transaction',
  `del_date_time` datetime NOT NULL COMMENT 'transaction deleted date time',
  `batch_id` int(11) NOT NULL,
  `batch_trans_id` int(11) NOT NULL,
  `post_status` int(2) NOT NULL,
  `pri_due_new` double(12,2) NOT NULL,
  `sec_due_new` double(12,2) NOT NULL,
  `tri_due_new` double(12,2) NOT NULL,
  `pat_due_new` double(12,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM") or $msg_info[] = imw_error();


imw_query("ALTER TABLE `tx_payments` ADD `batch_tx_id` INT( 11 ) NOT NULL");
imw_query("ALTER TABLE `tx_payments` ADD `payment_time` TIME NOT NULL");

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 29 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 29 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 29</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>