<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once('../../connect_imwemr.php'); // imwemr connection

$sql1="ALTER TABLE `superbill` ADD `arr_dx_codes` VARCHAR( 255 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `procedureinfo` ADD `dx5` VARCHAR( 20 ) NOT NULL ,
ADD `dx6` VARCHAR( 20 ) NOT NULL ,
ADD `dx7` VARCHAR( 20 ) NOT NULL ,
ADD `dx8` VARCHAR( 20 ) NOT NULL ,
ADD `dx9` VARCHAR( 20 ) NOT NULL ,
ADD `dx10` VARCHAR( 20 ) NOT NULL ,
ADD `dx11` VARCHAR( 20 ) NOT NULL ,
ADD `dx12` VARCHAR( 20 ) NOT NULL ;";
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error())
{
	$msg_info[] = "<br><br><b>Update 62 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 62 Success.</b>";
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