<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql="CREATE TABLE `specialty` (
	`specialty_id` int(10) NOT NULL AUTO_INCREMENT,
	`specialty_name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
	`del_status` varchar(10) COLLATE latin1_general_ci NOT NULL,
	PRIMARY KEY (`specialty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `users` ADD `specialty_id_multi` VARCHAR( 255 ) NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `procedures` ADD `specialty_id` VARCHAR( 255 ) NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 57 run OK";

?>

<html>
<head>
<title>Update 57</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







