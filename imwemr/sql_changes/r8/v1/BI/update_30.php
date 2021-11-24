<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("CREATE TABLE IF NOT EXISTS `previous_ub` (
  `previous_ub_id` int(12) NOT NULL AUTO_INCREMENT,
  `operator_id` int(12) NOT NULL,
  `patient_id` int(12) NOT NULL,
  `enc_id` int(12) NOT NULL,
  `created_date` date NOT NULL,
  `ub_satus` int(2) NOT NULL,
  `ub_data` longblob NOT NULL,
  `enc_balance` double(11,2) NOT NULL,
  PRIMARY KEY (`previous_ub_id`))") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 30 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 30 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 30</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>