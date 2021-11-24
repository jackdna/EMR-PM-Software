<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

$sql1 = "ALTER TABLE  `patient_data` ADD  `chk_notes_optical` INT( 1 ) NOT NULL DEFAULT  '0';";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 29 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 29 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 29</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>Add column to to show patient notes in optical</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>

</body>
</html>