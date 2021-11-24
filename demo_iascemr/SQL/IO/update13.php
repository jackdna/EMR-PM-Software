<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql="update `zip_codes` set `city`='Bethlehem' WHERE `zip_code` = '18017' ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql="update `zip_codes` set `city` = 'Whitehall' WHERE `zip_code` = '18052' ";
$row = imw_query($sql) or $msg_info[] = imw_error();

//START CODE TO INSERT NEW ZIP CODE
$chkQry = "select zip_id FROM zip_codes WHERE zip_code = '18320'";
$chkRes = imw_query($chkQry);
$insUptQry = "INSERT INTO";
$whrQry="";
if(imw_num_rows($chkRes)>0) {
	$insUptQry = " UPDATE ";	
	$whrQry = " WHERE zip_code = '18320' ";	
}
$sql= $insUptQry." zip_codes SET zip_code='18320', city='Analomik', state_abb='PA', state='Pennsylvania' ".$whrQry;
$row = imw_query($sql) or $msg_info[] = imw_error();
//END CODE TO INSERT NEW ZIP CODE

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 13 run OK";

?>

<html>
<head>
<title>Mysql Updates After Launch</title>
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







