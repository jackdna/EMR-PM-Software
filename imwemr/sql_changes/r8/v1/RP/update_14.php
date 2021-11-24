<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();

$res1 = imw_query("ALTER TABLE ".constant("IMEDIC_SCAN_DB").".`scan_doc_tbl`  ADD `direct_attach_id` INT NOT NULL COMMENT 'FK_id of direct_attachement table'");
$res2 = imw_query("ALTER TABLE `direct_messages_attachment`  ADD `is_cda` SMALLINT(1) NOT NULL DEFAULT '0' COMMENT '0=not ccda; 1= ccda document'");
$res3 = imw_query("ALTER TABLE `ccda_docs`  ADD `direct_attach_id` INT NOT NULL DEFAULT '0',  ADD `sch_id` INT NOT NULL DEFAULT '0'");

if($res1){
	$msg_info[] = 'New column added in "scan_doc_tbl".';
}else{
	$msg_info[] = imw_error();
}

if($res2){
	$msg_info[] = 'New column added in "direct_messages_attachment".';
}else{
	$msg_info[] = imw_error();
}

if($res3){
	$msg_info[] = 'New column added in "ccda_docs".';
}else{
	$msg_info[] = imw_error();
}

?>
<html>
<head>
<title>Release 8 Updates 14</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
Change done for PI scorecard (RT15).
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>