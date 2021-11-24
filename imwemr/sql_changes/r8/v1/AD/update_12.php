<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "
CREATE TABLE cds_intervention (
	id INT( 11 ) NOT NULL ,
	problem_list VARCHAR( 10 ) NOT NULL ,
	medication_list VARCHAR( 10 ) NOT NULL ,
	allergy_list VARCHAR( 10 ) NOT NULL ,
	laboratory_test VARCHAR( 10 ) NOT NULL ,
	vital_sign VARCHAR( 10 ) NOT NULL ,
	pt_gender VARCHAR( 10 ) NOT NULL ,
	combination VARCHAR( 255 ) NOT NULL 
) ENGINE = MYISAM ";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `cds_intervention` ADD PRIMARY KEY(`id`); ";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `cds_intervention` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 12 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 12 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 12</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>