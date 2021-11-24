<?php
//--- UPDATE CREATED BY ---
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");
////

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$q="ALTER TABLE `chart_master_table` ADD INDEX (`patient_id`) ;";
imw_query($q) or $msg_info[] = imw_error();
}

echo("Process done");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 28</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>