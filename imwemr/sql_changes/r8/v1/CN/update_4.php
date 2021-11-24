<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="
CREATE TABLE `chart_exam_ext` (
  `id` int(10) NOT NULL,
  `exam` varchar(50) NOT NULL,
  `tab` varchar(50) NOT NULL,
  `parent_obsrv` varchar(100) NOT NULL,
  `obsrv` varchar(100) NOT NULL,
  `htm_path` varchar(150) NOT NULL,
  `opid` int(10) NOT NULL,
  `opdt` datetime NOT NULL,
  `del` int(2) NOT NULL,
  `full_obsrv` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

";
imw_query($sql1) or $msg_info[] = imw_error();

$sql1="
ALTER TABLE `chart_exam_ext`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`opid`);
";
imw_query($sql1) or $msg_info[] = imw_error();

$sql1="
ALTER TABLE `chart_exam_ext`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";
imw_query($sql1) or $msg_info[] = imw_error();



/*************************************/

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 3 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 3 (CN)</title>
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