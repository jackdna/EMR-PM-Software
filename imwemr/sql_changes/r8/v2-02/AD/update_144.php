<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql_select='Select `id` from `tm_rules` where `tm_rule_name`="Outgoing Fax" ';
$select_row=imw_query($sql_select);
if($select_row && imw_num_rows($select_row)==0){
    $sql="INSERT INTO `tm_rules` (`id`, `tm_rcat_id`, `tm_rule_name`) VALUES (NULL, '1', 'Outgoing Fax') ";
    imw_query($sql) or $msg_info[] = imw_error();
}

$sql1="ALTER TABLE `tm_assigned_rules` ADD `outbound_fax` TEXT NOT NULL ";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 144 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 144 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 144</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>