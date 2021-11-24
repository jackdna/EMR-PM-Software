<?php
$ignoreAuth = true;  
include("../../../../config/globals.php");

$qry = " ALTER TABLE `users` ADD `sso_identifier` varchar(20) NOT NULL; ";
imw_query($qry) or $msg_info[]=imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 52  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 52  run successfully!</b>";
    $color = "green";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 52 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>