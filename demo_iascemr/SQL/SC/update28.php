<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
UPDATE eposted as ep, stub_tbl as st SET ep.stub_id=st.stub_id
WHERE  ep.patient_conf_id = st.patient_confirmation_id 
AND ep.patient_conf_id !='0'
AND ep.stub_id='0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
UPDATE eposted as ep, stub_tbl as st SET ep.stub_id=st.stub_id
WHERE  ep.patient_id = st.patient_id_stub 
AND ep.patient_id!='0'
AND ep.stub_id='0' 
AND ep.patient_conf_id ='0'
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
UPDATE scan_documents as sd, stub_tbl as st SET sd.stub_id=st.stub_id
WHERE  sd.confirmation_id = st.patient_confirmation_id
AND sd.confirmation_id!='0' 
AND sd.stub_id='0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
UPDATE scan_documents as sd, stub_tbl as st SET sd.stub_id=st.stub_id
WHERE  sd.patient_id = st.patient_id_stub
AND sd.patient_id!='0' 
AND sd.stub_id='0' 
AND sd.confirmation_id='0' 
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 28 run OK";

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







