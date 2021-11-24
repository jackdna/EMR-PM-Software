<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$error = array();
$qry = array();
$qry[] = "ALTER TABLE  `patient_data` ADD  `fmh_pt_status` VARCHAR( 20 ) NOT NULL DEFAULT  'pending'";

foreach($qry as $sql)
{
	imw_query($sql) or $error[] = imw_error();
}

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 3 Success.</b>";
	$color = "green";	
}

?>

<html>
<head>
<title>Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>