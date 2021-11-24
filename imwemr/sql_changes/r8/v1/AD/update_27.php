<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "UPDATE copay_policies SET EmdeonUrl = REPLACE(EmdeonUrl, 'emdeon.com', 'changehealthcare.com') WHERE 1 ";
imw_query($qry) or $msg_info[] = imw_error();


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 27 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 27 completed successfully. Emdeon URL updated</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 27</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>