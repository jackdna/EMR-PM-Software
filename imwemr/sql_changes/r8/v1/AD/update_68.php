<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

        
$qry="ALTER TABLE `tm_rules_list`
ADD `appt_procedure` VARCHAR( 1024 ) NOT NULL ,
ADD `appt_ref_phy` VARCHAR( 2048 ) NOT NULL,
ADD `ss_id` int(10) NOT NULL,
ADD `ss_type` varchar(255) NOT NULL,
ADD `tm_group` VARCHAR( 255 ) NOT NULL,
ADD `ar_facility` VARCHAR( 512 ) NOT NULL,
ADD `tm_ins_group` VARCHAR( 1024 ) NOT NULL,
ADD `tm_ins_comp` VARCHAR( 2048 ) NOT NULL,
ADD `tm_cpt_code` VARCHAR( 512 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry1="ALTER TABLE `tm_assigned_rules`
ADD `appt_ref_phy_id` INT( 10 ) NOT NULL, 
ADD `group_name` VARCHAR( 255 ) NOT NULL ,
ADD `ins_group` VARCHAR( 255 ) NOT NULL ,
ADD `ins_comp` VARCHAR( 255 ) NOT NULL ,
ADD `payment_comtId` int(10) NOT NULL,
ADD `notes_users` varchar(512) NOT NULL,
ADD `reminder_date` date NOT NULL";
imw_query($qry1) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 68 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 68 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 68</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>