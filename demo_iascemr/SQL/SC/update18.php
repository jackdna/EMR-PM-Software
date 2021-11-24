<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
ALTER TABLE `narcotics_log_tbl` ADD `total_amount_issued` VARCHAR( 255 ) NOT NULL ,
ADD `amount_administered` VARCHAR( 255 ) NOT NULL ,
ADD `amount_wasted` VARCHAR( 255 ) NOT NULL ,
ADD `amount_returned` VARCHAR( 255 ) NOT NULL ,
ADD `amount_accounted_for` VARCHAR( 255 ) NOT NULL ,
ADD `signature` VARCHAR( 255 ) NOT NULL ,
ADD `co_signature` VARCHAR( 255 ) NOT NULL 
";
$row = imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 18 run OK";

//line 12323
?>

<html>
<head>
<title>Mysql Updates For Query Optimization</title>
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







