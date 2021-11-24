<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "ALTER TABLE  `schedule_status` ADD  `col_type` TINYINT NOT NULL DEFAULT  '2' COMMENT '0 => Mandatory Fields , 1 => Status Fields, 2 => Custom Fields[ Default value for new custom status ]' AFTER  `status` ";

$sql[] = "update schedule_status set col_type = 0 where id in (11,13,18,201,202,203,271)";
$sql[] = "update schedule_status set col_type = 1 where id in (1,2,3,4,5,6,7,8,9,10,12,14,15,16,17,21,22,23,100,101,200)";

foreach(  $sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 50 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 50 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 50</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>