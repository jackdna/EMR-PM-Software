<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");


$sql1="ALTER TABLE `operatingroomrecords` 
		ADD `injXylocaineMPF` CHAR( 3 ) NOT NULL ,
		ADD `injXylocaineMPFList` VARCHAR( 20 ) NOT NULL ,
		ADD `injMiostat` CHAR( 3 ) NOT NULL ,
		ADD `injMiostatList` VARCHAR( 20 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 113 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 113 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 113</title>
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