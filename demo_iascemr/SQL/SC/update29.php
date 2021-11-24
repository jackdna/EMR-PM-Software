<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
UPDATE scan_upload_tbl as sut, stub_tbl as st SET sut.stub_id=st.stub_id
WHERE  sut.confirmation_id = st.patient_confirmation_id
AND sut.confirmation_id != '0' 
AND sut.stub_id='0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
UPDATE scan_upload_tbl as sut, stub_tbl as st SET sut.stub_id=st.stub_id
WHERE  sut.patient_id  = st.patient_id_stub
AND sut.patient_id != '0' 
AND sut.stub_id='0' 
AND sut.confirmation_id = '0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();


if(constant("ARCHIVE_SCAN_DB")!='') {
	$sql = "
	UPDATE ".constant("ARCHIVE_SCAN_DB").".scan_upload_tbl as sut, stub_tbl as st SET sut.stub_id=st.stub_id
	WHERE  sut.confirmation_id = st.patient_confirmation_id
	AND sut.confirmation_id != '0'
	AND sut.stub_id='0' 
	";
	$row = imw_query($sql) or $msg_info[] = imw_error();
	
	$sql = "
	UPDATE ".constant("ARCHIVE_SCAN_DB").".scan_upload_tbl as sut, stub_tbl as st SET sut.stub_id=st.stub_id
	WHERE  sut.patient_id = st.patient_id_stub
	AND sut.patient_id != '0'
	AND sut.stub_id='0' 
	AND sut.confirmation_id = '0'
	";
	$row = imw_query($sql) or $msg_info[] = imw_error();
	
}

$sql = "
UPDATE scan_log_tbl as slt, stub_tbl as st SET slt.stub_id=st.stub_id
WHERE  slt.confirmation_id = st.patient_confirmation_id
AND slt.confirmation_id != '0' 
AND slt.stub_id='0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
UPDATE scan_log_tbl as slt, stub_tbl as st SET slt.stub_id=st.stub_id
WHERE  slt.patient_id = st.patient_id_stub
AND slt.patient_id != '0' 
AND slt.stub_id='0' 
AND slt.confirmation_id = '0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 29 run OK";

?>

<html>
<head>
<title>Mysql Updates For Query Optimization</title>
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







