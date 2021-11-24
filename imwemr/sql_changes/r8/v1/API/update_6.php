<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$error = array();
$createTbl = "CREATE TABLE `patient_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation` varchar(200) NOT NULL,
  `del_status` TINYINT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"; 

$resp = imw_query($createTbl) or $error[] = imw_error();
if($resp){
	$dummyData = "INSERT INTO `patient_relations` (`relation`) VALUES ('Aunt'),('Uncle'),('Brother'),('Sister'),('Child:No Fin Responsibility'),('Daughter'),('Dep Child:Fin Responsibility'),('Donor Live'),('Donor-Dceased'),('Employee'),('Father'),('Foster Child'),('Friend'),('Grand Child'),('Grandparent'),('Guardian'),('Handicapped Dependant'),('Injured Plantiff'),('Inlaw'),('Legal Guardian'),('Minor Dependent Of a Dependent'),('Mother'),('Niece'),('Nephew'),('POA'),('Relative'),('Son'),('Sponsored Dependent'),('Spouse'),('Step Child'),('Student'),('Ward of The Court'),('Self'),('Other')";
	imw_query($dummyData) or $error[] = imw_error();
}

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 6 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 6 Success.</b>";
	$color = "green";	
}

?>

<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>