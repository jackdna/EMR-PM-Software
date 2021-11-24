<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();


$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'indx_trans_dot'";
$row=imw_query($sql);
if($row){
	$sql="CREATE INDEX indx_trans_dot ON report_enc_trans(trans_dot);";
	$row = imw_query($sql)or $msg_info[] = imw_error();
	if($row){
		$msg_info[]='indx_trans_dot created successfuly';
	}
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'indx_trans_dop'";
$row=imw_query($sql);
if($row){
	$sql="CREATE INDEX indx_trans_dop ON report_enc_trans(trans_dop);";
	$row = imw_query($sql)or $msg_info[] = imw_error();
	if($row){
		$msg_info[]='indx_trans_dop created successfuly';
	}
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'indx_date_of_service'";
$row=imw_query($sql);
if($row){
	$sql="CREATE INDEX indx_date_of_service ON report_enc_detail(date_of_service);";
	$row = imw_query($sql)or $msg_info[] = imw_error();
	if($row){
		$msg_info[]='indx_date_of_service created successfuly';
	}	
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'indx_encounter_id'";
$row=imw_query($sql);
if($row){
	$sql="CREATE INDEX indx_encounter_id ON report_enc_detail(encounter_id);";
	$row = imw_query($sql)or $msg_info[] = imw_error();
	if($row){
		$msg_info[]='indx_encounter_id created successfuly';
	}	
}


?>
<html>
<head>
<title>Release 8 Updates 15</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

<br>
<br>
    <font face="Arial, Helvetica, sans-serif" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>