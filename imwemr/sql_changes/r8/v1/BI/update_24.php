<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `cpt_fee_tbl` ADD `clia_proc` SMALLINT NOT NULL DEFAULT '0' COMMENT '0=normal; 1 =clia need to send'") or $msg_info[] = imw_error();

imw_query("ALTER TABLE `facility` CHANGE `clai_usages` `clia_usages` SMALLINT( 6 ) NOT NULL DEFAULT '0' COMMENT '0=selective CPTs; 1=all CPTs'");
imw_query("ALTER TABLE `facility` DROP `clai_usages`");
imw_query("ALTER TABLE `facility` ADD `clia_usages` SMALLINT NOT NULL DEFAULT '0' COMMENT '0=selective CPTs; 1=all CPTs'");

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 24 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 24 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 24</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>