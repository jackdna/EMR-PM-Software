<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE  `patient_physician_orders` ADD  `physician_order_type` VARCHAR( 255 ) NOT NULL "; 

//$sql[] = "CREATE  TABLE patient_physician_orders_".date("d_m_Y")." LIKE patient_physician_orders; "; 
//$sql[] = "INSERT INTO patient_physician_orders_".date("d_m_Y")." (SELECT *  FROM patient_physician_orders); "; 
//$sql[] = "UPDATE patient_physician_orders pp, postopphysicianorders po SET  pp.physician_order_type = 'medication' WHERE pp.physician_order_type = '' AND (pp.chartName = 'post_op_physician_order_form' OR pp.chartName = 'post_op_nursing_form') AND po.form_status != '' AND pp.confirmation_id = po.patient_confirmation_id "; 

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 168 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 168 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 168</title>
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