<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1 = "ALTER TABLE `patient_data` ADD `External_MRN_5` VARCHAR(50) COMMENT 'DSS Patient DFN'";
imw_query($sql1) or $error[] = imw_error();

$sql2 = "ALTER TABLE `allergies_data` ADD `dss_id` VARCHAR(50) NOT NULL COMMENT 'DSS Allergy Id'";
imw_query($sql2) or $error[] = imw_error();

$sql3 = "ALTER TABLE `lists` ADD `dss_allergy_id` VARCHAR(50) NOT NULL COMMENT 'DSS Allergy Id'";
imw_query($sql3) or $error[] = imw_error();

$sql4 = "ALTER TABLE `pt_problem_list` ADD `dss_prblm_id` INT( 10 ) NOT NULL COMMENT 'dss problem ifn'";
imw_query($sql4) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 1 Success.</b>";
	$color = "green";	
}

?>

<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>