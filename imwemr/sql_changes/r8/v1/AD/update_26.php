<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE `cpt_fee_tbl` ADD `valueSet` VARCHAR( 50 ) NOT NULL ;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `rad_test_data` ADD `rad_order_time` TIME NOT NULL , ADD `rad_results_time` TIME NOT NULL ;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `lists` ADD `begtime` TIME NOT NULL ,ADD `endtime` TIME NOT NULL ";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `hc_observations` ADD `observation_time` TIME NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `lists` ADD `refusal` TINYINT NOT NULL , ADD `refusal_reason` TEXT NOT NULL , ADD `refusal_snomed` TEXT NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `rad_test_data` ADD `refusal` TINYINT NOT NULL , ADD `refusal_reason` TEXT NOT NULL , ADD `refusal_snomed` TEXT NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

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