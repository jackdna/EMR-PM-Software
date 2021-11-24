<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
$afctRw=0;
if(constant('ARCHIVE_SCAN_DB')) {
	$archiveScanDbName = constant('ARCHIVE_SCAN_DB').".";

	$qryCnt = "INSERT INTO scan_upload_tbl (SELECT * FROM ".$archiveScanDbName."scan_upload_tbl WHERE scan_upload_id NOT IN(SELECT scan_upload_id FROM scan_upload_tbl))";
	$resCnt = imw_query($qryCnt) or $msg_info[] = imw_error();
	$afctRw = imw_affected_rows();
}
$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Total rows affected ".$afctRw." <br>Archived records added sucessfully";

?>

<html>
<head>
<title>Add Archived Records To Original Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







