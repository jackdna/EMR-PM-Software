<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="
CREATE TABLE IF NOT EXISTS `patientPayer` (
  `id` int(110) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payer` varchar(110) NOT NULL,
  `pid` int(110) NOT NULL,
  `dos` date NOT NULL,
  `formId` int(110) NOT NULL,
  `valueCode` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
";
imw_query($sql1) or $msg_info[] = imw_error();


/*************************************/

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 15 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 15 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 15 (CN)</title>
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