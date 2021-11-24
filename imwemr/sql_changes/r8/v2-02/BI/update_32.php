<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();
$curr_date=date('Y-m-d');
imw_query("ALTER TABLE `previous_statement` ADD `statement_acc_status` INT( 1 ) NOT NULL ,ADD `statement_acc_count` INT( 1 ) NOT NULL ,ADD `statement_acc_date` TEXT NOT NULL ,ADD `statement_acc_chl` TEXT NOT NULL ");
imw_query("update previous_statement set statement_acc_status='1' where statement_acc_status=0 and created_date<='$curr_date'");
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 32 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 32 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 32</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>