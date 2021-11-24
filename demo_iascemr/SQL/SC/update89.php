<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once('../../connect_imwemr.php'); // imwemr connection

$sql1="ALTER TABLE `lists` ADD `scemr_confirmation_id` int( 11 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();



if(imw_error())
{
	$msg_info[] = "<br><br><b>Update 89 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 89 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 89</title>
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