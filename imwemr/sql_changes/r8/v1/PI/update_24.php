<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql2 = "ALTER TABLE ".$sqlconf['scan_db_name'].".`scan_doc_tbl` CHANGE  `patient_id`  `patient_id` BIGINT( 255 ) NOT NULL";
imw_query($sql2) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 24 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 24 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 24</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>