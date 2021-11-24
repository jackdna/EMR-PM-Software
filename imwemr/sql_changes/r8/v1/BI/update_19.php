<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `patient_charge_list_details` ADD `pmt_notes` TEXT NOT NULL ") or $msg_info[] = imw_error();

imw_query("CREATE TABLE tx_charges (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,patient_id INT( 11 ) NOT NULL ,encounter_id INT( 11 ) NOT NULL ,charge_list_id INT( 11 ) NOT NULL ,
charge_list_detail_id INT( 11 ) NOT NULL ,new_charges DOUBLE( 12, 2 ) NOT NULL ,old_charges DOUBLE( 12, 2 ) NOT NULL ,entered_date DATETIME NOT NULL ,operator_id INT( 11 ) NOT NULL ,
del_status INT( 2 ) NOT NULL ,del_date_time DATETIME NOT NULL ,del_operator_id INT( 11 ) NOT NULL)") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 19 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 19 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 19</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>