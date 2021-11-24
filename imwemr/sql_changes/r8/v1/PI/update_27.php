<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$bkupTable = "patient_data_".(date('Y_m_d'));
$sql1 = "CREATE TABLE ".$bkupTable."  LIKE patient_data;";
imw_query($sql1) or $error[] = imw_error();

$sql2 = "INSERT ".$bkupTable."  SELECT * FROM patient_data;";
imw_query($sql2) or $error[] = imw_error();

$sql3 = "ALTER TABLE  `patient_data` CHANGE  `preferr_contact`  `preferr_contact` TINYINT( 4 ) NOT NULL DEFAULT  '2' COMMENT  '0=Home Phone, 1 = Work Phone, 2 = Mobile Phone';";
imw_query($sql3) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 27 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 27 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 27</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>Set Default value of preferred contact method from home phone to mobile</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>