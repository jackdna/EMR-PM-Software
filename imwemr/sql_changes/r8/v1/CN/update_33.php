<?php
//--- UPDATE CREATED BY ---
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");
////


$q="ALTER TABLE `tests_version` CHANGE `test_results` `test_results` MEDIUMTEXT NOT NULL";
imw_query($q) or $msg_info[] = imw_error();


echo("Column length change done");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 32</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>