<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "CREATE TABLE `denial_resp` (`denial_resp_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`cpt_code_resp` TEXT NOT NULL ,`cas_code_resp` TEXT NOT NULL ,`denial_resp_all` INT( 2 ) NOT NULL ,
		`modified_date` DATETIME NOT NULL ,`modified_by` INT( 11 ) NOT NULL);";

$sql[] = "insert into denial_resp set  denial_resp_id='1'";

$sql[] = "ALTER TABLE `deniedpayment` CHANGE `CAS_type` `CAS_type` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL";

$sql[] = "ALTER TABLE `defaultwriteoff` ADD `cas_type` VARCHAR( 50 ) NOT NULL ,ADD `cas_code` VARCHAR( 50 ) NOT NULL";

if(strtolower($billing_global_server_name)!='clarisvision'){
	//$sql[] = "update deniedpayment set next_responsible_by='1' where next_responsible_by='0'";
	//$sql[] = "update denial_resp set denial_resp_all='1' where denial_resp_id='1'";
}


foreach($sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 16 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 16 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 16</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>