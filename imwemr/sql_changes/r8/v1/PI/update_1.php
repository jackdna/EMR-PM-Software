<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="CREATE TABLE `erx_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curl_url` varchar(500) NOT NULL,
  `request_data` text NOT NULL,
  `response_data` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `request_datetime` datetime NOT NULL,
  `error` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 1 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 1 (BI)</title>
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