<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="ALTER TABLE `hl7_sent`  ADD `sftp_sent` SMALLINT NOT NULL DEFAULT '1' COMMENT 'for multi type sender',  ADD `sftp_sent_on` DATETIME NOT NULL";  // to set previous messages as sent.
$sql[]="ALTER TABLE `hl7_sent` CHANGE `sftp_sent` `sftp_sent` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT 'for multi type sender'"; //for new upcoming messages.

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 3 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 3 (HL7)</title>
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