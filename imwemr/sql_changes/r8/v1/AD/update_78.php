<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

imw_query("ALTER TABLE `tm_assigned_rules` ADD `sa_doctor_id_task` INT( 10 ) NOT NULL AFTER `operatorid`");

$msg_info[] = "<br><b>Release :<br> Update 78 Success.</b>";

$color = "green";	

?>
<html>
<head>
<title>Update 78</title>
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