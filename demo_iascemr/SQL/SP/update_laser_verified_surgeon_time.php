<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$curDate = date('Y-m-d');
$tbl_data="laser_procedure_patient_table_X".$curDate;
imw_query("CREATE TABLE ".$tbl_data." AS (SELECT * FROM laser_procedure_patient_table)")or $msg_info[] = imw_error();
	
$q=imw_query("Update laser_procedure_patient_table set verified_surgeon_timeout = signSurgeon1DateTime WHERE signSurgeon1DateTime <> '0000-00-00 00:00:00' AND verified_surgeon_timeout = '0000-00-00 00:00:00' ");
$r = imw_affected_rows();

$msg_info[] = "<br><br><b>Update verified surgeon timeout in laser chart Successful. $r records updated.</b><br>";
$color = "green";			
?>
<html>
<head>
<title>Update verified surgeon timeout in laser chart</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body><br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>