<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `stub_tbl` 
ADD `comment_modified_status` INT( 5 ) NOT NULL AFTER `comment` ,
ADD `comment_modified_datetime` DATETIME NOT NULL AFTER `comment_modified_status` ,
ADD `comment_modified_by_operator` INT( 11 ) NOT NULL AFTER `comment_modified_datetime`,
ADD `comment_status_reset_datetime` DATETIME NOT NULL AFTER `comment_modified_by_operator`,  
ADD `comment_status_reset_by_operator` INT( 11 ) NOT NULL AFTER `comment_status_reset_datetime`
"; 
imw_query($sql1)or $msg_info[] = imw_error();



if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 122 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 122 Success.</b><br>".$message;
	$color = "green";			
}

?>
<html>
<head>
<title>Update 122</title>
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