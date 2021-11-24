<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl WHERE key_name = 'scandoctbl_ptidtaskphyidtaskstatus' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX scandoctbl_ptidtaskphyidtaskstatus ON ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl(patient_id,task_physician_id,task_status);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 8: Create Index run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 8: Create Index run successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Create Indexes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color_sts;?>" size="2">
    <?php echo(@implode("<br>",$msg_info_sts));?>
</font>
</body>
</html>