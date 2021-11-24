<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../../common/conDb.php");

$sql1="CREATE TABLE `complications` (
		`complicationsId` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL,
		`isDefault` int(11) NOT NULL,
		PRIMARY KEY (`complicationsId`)
		) AUTO_INCREMENT=1 ;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `operatingroomrecords` ADD `complications` TEXT NOT NULL;"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$message = "<br><br><b>Update 5 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";
}
else
{	
	$message = "<br><br><b>Update 5 Success.</b><br>";
	$color = "green";			
}

?>

<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo $message;?></font>
<?php
@imw_close();
}
?> 
</body>
</html>