<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `electronicfiles_tbl`  ADD `read_status` SMALLINT NOT NULL DEFAULT '1' COMMENT '1=read by 835 parsing programm',  ADD `read_on` DATETIME NOT NULL COMMENT 'when 835 parsing done'") or $msg_info[] = imw_error();

imw_query("ALTER TABLE `electronicfiles_tbl` CHANGE `read_status` `read_status` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT '1=read by 835 parsing programm'") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 20 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 28 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 28</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>