<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE `facility` ADD `fac_tin` VARCHAR( 15 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "CREATE TABLE `communication` (`id` int(11) NOT NULL, `patient_id` int(11) NOT NULL, `form_id` int(11) NOT NULL, `description` longtext NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `communication` ADD PRIMARY KEY (`id`);";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `communication` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;"; 
imw_query($qry) or $msg_info[] = imw_error();


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 28 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 28 completed successfully. Emdeon URL updated</b>";
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