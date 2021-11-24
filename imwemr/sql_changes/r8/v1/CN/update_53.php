<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = " CREATE TABLE `sso_log` (
  `id` int(10) NOT NULL,
  `token` varchar(200) NOT NULL,
  `uid` int(10) NOT NULL,
  `dtime` datetime NOT NULL,
  `status` varchar(100) NOT NULL,
  `url` varchar(250) NOT NULL
) ENGINE=MyISAM ";
imw_query($qry) or $msg_info[]=imw_error();

$qry = "ALTER TABLE `sso_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);";
 imw_query($qry) or $msg_info[]=imw_error(); 
  
$qry = "ALTER TABLE `sso_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
 imw_query($qry) or $msg_info[]=imw_error(); 
  



if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 53  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 53  run successfully!</b>";
    $color = "green";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 53 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>