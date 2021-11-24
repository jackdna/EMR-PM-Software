<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `laser_procedure_patient_table` 
			ADD `sign_all_pre_op_order_status` TINYINT( 1 ) NOT NULL ,
			ADD `sign_all_post_op_order_status` TINYINT( 1 ) NOT NULL ,
			ADD INDEX sign_all_pre_op_order_status(sign_all_pre_op_order_status), 
			ADD INDEX sign_all_post_op_order_status(sign_all_post_op_order_status);";


foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$chkQry = "SELECT laser_procedureRecordID FROM laser_procedure_patient_table WHERE sign_all_pre_op_order_status = '1' OR sign_all_post_op_order_status = '1' LIMIT 0,1";
$chkRes = imw_query($chkQry)or $msg_info[] = imw_error();
if(imw_num_rows($chkRes)==0) {
	$updtQry = "UPDATE laser_procedure_patient_table SET sign_all_pre_op_order_status = '1', sign_all_post_op_order_status = '1' WHERE form_status != '' AND signSurgeon1Id != '0' ";
	$updtRes = imw_query($updtQry)or $msg_info[] = imw_error();

	/*
	$updtQry = "UPDATE laser_procedure_patient_table lp
				INNER JOIN patientconfirmation pc ON pc.patientConfirmationId = lp.confirmation_id AND pc.ascId !='0' AND lp.form_status = 'not completed'
				SET lp.sign_all_pre_op_order_status = '1', lp.sign_all_post_op_order_status = '1'";
	$updtRes = imw_query($updtQry)or $msg_info[] = imw_error();
	*/
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 184 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 184 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 184</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>