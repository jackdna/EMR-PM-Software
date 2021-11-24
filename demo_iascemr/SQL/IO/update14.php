<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

//START CODE TO INSERT NEW ZIP CODE
$zipArr = array("18109"=>"Allentown@@PA@@Pennsylvania", "18065"=>"Neff@@PA@@Pennsylvania", "18335"=>"Marshalls Creek@@PA@@Pennsylvania", "18935"=>"Milford Square@@PA@@Pennsylvania");
foreach($zipArr as $key =>$val) {
	$chkQry = "select zip_id FROM zip_codes WHERE zip_code = '".$key."'";
	$chkRes = imw_query($chkQry);
	$insUptQry = "INSERT INTO";
	$whrQry="";
	list($city,$state_abb,$state) = explode("@@",$val);
	if(imw_num_rows($chkRes)>0) {
		$insUptQry = " UPDATE ";	
		$whrQry = " WHERE zip_code = '".$key."' ";	
	}
	$sql= $insUptQry." zip_codes SET zip_code='".$key."', city='".$city."', state_abb='".$state_abb."', state='".$state."' ".$whrQry;
	$row = imw_query($sql) or $msg_info[] = imw_error();
}
//END CODE TO INSERT NEW ZIP CODE

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 14 run OK";

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







