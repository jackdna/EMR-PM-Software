<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
imw_query("ALTER TABLE report_enc_trans ADD trans_source TEXT NOT NULL") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `copay_policies` ADD `rcd_finished` DATETIME NOT NULL") or $msg_info[] = imw_error();
if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 5 Failed!</b>";
	$color = "red";
}
else{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 5 successfull</b>";
	$color = "green";

}
?>
<html>
<head>
<title>Release 8 Updates 5 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>