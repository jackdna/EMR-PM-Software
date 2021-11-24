<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("CREATE TABLE IF NOT EXISTS `transaction_modify` (
  `trans_id` int(11) NOT NULL AUTO_INCREMENT,
  `master_tbl_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `charge_list_id` int(11) NOT NULL,
  `charge_list_detail_id` int(11) NOT NULL,
  `trans_by` varchar(250) NOT NULL,
  `trans_ins_id` int(11) NOT NULL,
  `trans_method` varchar(250) NOT NULL,
  `check_number` varchar(20) NOT NULL,
  `cc_type` varchar(250) NOT NULL,
  `cc_number` varchar(20) NOT NULL,
  `cc_exp_date` varchar(20) NOT NULL,
  `trans_type` varchar(250) NOT NULL,
  `trans_amount` double(12,2) NOT NULL,
  `trans_dot` date NOT NULL COMMENT 'Trans Entered Date time',
  `trans_dot_time` time NOT NULL,
  `trans_dop` date NOT NULL,
  `trans_dop_time` time NOT NULL,
  `modified_date` date NOT NULL,
  `modified_by` int(11) NOT NULL,
  `trans_operator_id` int(11) NOT NULL,
  `trans_del_date` date NOT NULL,
  `trans_del_time` time NOT NULL,
  `trans_del_operator_id` int(11) NOT NULL,
  `cas_type` varchar(50) NOT NULL,
  `cas_code` varchar(50) NOT NULL,
  `date_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `facility_id` int(11) NOT NULL,
  PRIMARY KEY (`trans_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 26 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 26 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 26</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>