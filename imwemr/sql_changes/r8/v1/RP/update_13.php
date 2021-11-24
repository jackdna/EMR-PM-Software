<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();

$res1 = imw_query("SELECT * FROM `custom_reports` WHERE `template_name` LIKE '2020 MIPS PI' ORDER BY id");
if($res1 && imw_num_rows($res1)==0){
	$sql = "INSERT INTO `custom_reports`(`id`,`template_name`,`template_fields`,`delete_status`,`default_report`,`report_type`,`report_sub_type`) ";
	$sql.= "VALUES (NULL , '2020 MIPS PI', '', '0', '1', 'compliance', 'mur')";
	$res2 = imw_query($sql);
	$rpt_id = imw_insert_id();
	if($res2){
		$msg_info[] = 'New compliance Report added - 2020 MIPS PI.';
	}else{$msg_info[] = imw_error();}
}else if($res1 && imw_num_rows($res1)>0){
	$rs1 = imw_fetch_assoc($res1);
	$rpt_id = $rs1['id'];
	$res2 = imw_query("UPDATE `custom_reports` SET `delete_status` = '0' WHERE id = '".$rpt_id."'");
	if($res2){
		$msg_info[] = 'Report added - 2020 MIPS PI.';
	}else{$msg_info[] = imw_error();}
}else{
	$msg_info[] = 'Update already executed.';	
}
imw_query("DELETE FROM `custom_reports` WHERE template_name = '2020 MIPS PI' AND id!='".$rpt_id."'");

?>
<html>
<head>
<title>Release 8 Updates 13 (RP)</title>
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