<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$statusArr = array(
	0 => array('status_name' => 'Patient Portal Demographics Updated', 'alias' => 'PPD', 'status' => 0, 'col_type' => 0),
	1 => array('status_name' => 'Patient Portal Medical Hx Updated', 'alias' => 'PPM', 'status' => 0, 'col_type' => 0),
	2 => array('status_name' => 'Patient Portal Consent Updated', 'alias' => 'PPC', 'status' => 0, 'col_type' => 0)
);


foreach($statusArr as $key => $obj){
	//Check Table if any record with this name already exists or not
	$chkQry = imw_query(" SELECT id FROM schedule_status WHERE UPPER(alias) = '".strtoupper($obj['alias'])."' ");
	if(!$chkQry){
		$msg_info[] = imw_error();
		continue;
	}
	
	if(imw_num_rows($chkQry) == 0){
		$addrec = AddRecords($obj,'schedule_status');
		if(!$addrec) $msg_info[] = imw_error();
	}else{
		$rowData = imw_fetch_assoc($chkQry);
		$updateRec = UpdateRecords($rowData['id'],'id',$obj,'schedule_status');
		if(!$updateRec) $msg_info[] = imw_error();
	}
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 6 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 6 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>