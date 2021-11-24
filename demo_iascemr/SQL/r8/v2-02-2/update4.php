<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../../common/conDb.php");

$sql1="ALTER TABLE `procedures` ADD `units` INT( 11 ) NOT NULL , ADD INDEX units(units);"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$message = "<br><br><b>Update 4 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";
}
else
{	
	$message = "<br><br><b>Update 4 Success.</b><br>";
	$color = "green";			
}

?>

<html>
<head>
<title>Update 4</title>
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