<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "ALTER TABLE  `paymentscomment` ADD  `task_assign` TINYINT NOT NULL COMMENT  'Is This a Task 1 > No 2 > Yes' DEFAULT  '1',
ADD  `task_done` TINYINT NOT NULL COMMENT  'Is This Task done  1 > No 2 > Yes' DEFAULT  '1',
ADD  `task_assign_by` INT( 11 ) NOT NULL COMMENT  'Task Marked By',
ADD  `task_assign_for` VARCHAR( 250 ) NOT NULL COMMENT  'Task Marked For',
ADD  `task_assign_date` DATETIME NOT NULL ,
ADD  `task_modify_date` DATETIME NOT NULL ;";

foreach(  $sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 10 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 10 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 9</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>