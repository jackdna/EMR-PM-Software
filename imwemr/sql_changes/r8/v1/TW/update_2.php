<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "ALTER TABLE `lists` ADD `as_ddi` VARCHAR(50) NOT NULL COMMENT 'TW DDI for the medicine'";
/*$qry[] = "ALTER TABLE `lists` DROP COLUMN `as_ddi`";*/

foreach ($qry  as $sql){
	imw_query($sql) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>TouchWorks Update 2 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>TouchWorks Update 2 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>