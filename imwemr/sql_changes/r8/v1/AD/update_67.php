<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$createTblSql = imw_query("CREATE TABLE IF NOT EXISTS `ref_multi_direct_mail` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `ref_id` INT(11) NOT NULL, `email` VARCHAR(255) NOT NULL, `del_status` TINYINT NOT NULL DEFAULT  '0', `default` TINYINT NOT NULL DEFAULT  '0', `del_by` INT(11) NOT NULL) ENGINE = MyISAM; ") or die(imw_error());

if(!$createTblSql){
	$msg_info[] = "<br><br><b>Update 66 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 66 success!</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 66</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>