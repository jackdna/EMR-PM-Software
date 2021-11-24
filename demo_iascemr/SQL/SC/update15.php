<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "SELECT `categoryId` FROM `preopmedicationcategory` order by `categoryId`";
$res = imw_query($sql) or $msg_info[] = imw_error();
$catIdArr = array();
$updatedRecord = '0';
if(imw_num_rows($res)>0) {
	$catIdArr[]=0;
	while($row = imw_fetch_array($res)) {
		$catIdArr[] = $row["categoryId"];			
	}
	$catIdImplode = implode(",",$catIdArr);
	$updtQry = "UPDATE preopmedicationorder SET mediCatId='0' WHERE mediCatId NOT IN(".$catIdImplode.")";
	$updtRes = imw_query($updtQry) or $msg_info[] = imw_error();
	$updatedRecord =  imw_affected_rows($updtRes);
	printf ("Total Updated Record(s) =  %d\n", imw_affected_rows());

}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 15 to set unassigned Pre-Op Medication to Default Category run OK";
?>

<html>
<head>
<title>Mysql Updates</title>
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







