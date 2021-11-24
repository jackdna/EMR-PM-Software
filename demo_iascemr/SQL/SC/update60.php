<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once('../../connect_imwemr.php'); // imwemr connection

$sql1="ALTER TABLE `superbill` ADD `sup_icd10` INT( 2 ) NOT NULL ";
imw_query($sql1);
if(imw_error())
{
	$msg_info[] = "<br><br><b>Update 60 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 60 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 60</title>
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