<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

	imw_query("ALTER TABLE `insurance_companies` ADD `claim_filing_days` SMALLINT( 5 ) NOT NULL ,
				ADD `payment_due_days` SMALLINT( 5 ) NOT NULL") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 140 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 140 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 140</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>