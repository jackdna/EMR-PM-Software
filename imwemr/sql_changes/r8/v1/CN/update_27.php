<?php
//--- UPDATE CREATED BY ---
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");
////

$sql="
ALTER TABLE ".constant("IMEDIC_SCAN_DB").".idoc_drawing ADD `deletedby` INT ( 10 ) NOT NULL 
";
imw_query($q) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 97 run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 97 :: Update run successfully!";
	$color = "green";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 97</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>