<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();

$res1 = imw_query("SELECT * FROM `custom_reports` WHERE `template_name` LIKE 'ACI 2019' AND `delete_status` = 0");
if($res1 && imw_num_rows($res1)==0){
	$sql = "INSERT INTO `custom_reports`(`id`,`template_name`,`template_fields`,`delete_status`,`default_report`,`report_type`,`report_sub_type`) ";
	$sql.= "VALUES (NULL , 'ACI 2019', '', '0', '1', 'compliance', 'mur')";
	$res2 = imw_query($sql);
	if($res2){
		$msg_info[] = 'New compliance Report added - ACI 2019.';
	}else{$msg_info[] = imw_error();}
}else{
	$msg_info[] = 'Update already executed.';	
}


?>
<html>
<head>
<title>Release 8 Updates 8 (RP)</title>
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