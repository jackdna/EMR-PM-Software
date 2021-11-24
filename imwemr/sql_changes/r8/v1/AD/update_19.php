<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "ALTER TABLE ccda_export_schedule ADD enc_key VARCHAR( 255 ) NOT NULL ";

foreach($qry as $q){imw_query($q) or $msg_info[] = imw_error();}



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