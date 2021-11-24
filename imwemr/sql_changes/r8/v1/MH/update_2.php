<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//Create New Table
$createTable = imw_query("CREATE TABLE IF NOT EXISTS `snomed_valueset` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `code` varchar(255) NOT NULL,
		  `value_set` varchar(255) NOT NULL,
		  `valueset_text` varchar(255) NOT NULL,
		  `type` varchar(255) NOT NULL,
		  `code_system` varchar(255) NOT NULL,
		  `page` varchar(255) NOT NULL,
		  `pid` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1") or $msg_info[] = imw_error();	


//$sql1 = "INSERT INTO `snomed_valueset` (`code`, `value_set`, `valueset_text`, `type`) VALUES
//('71485-7', '2.16.840.1.113883.3.526.3.1333', 'Diagnostic Study, Performed: Cup to Disc Ratio', 'lionic'),
//('71487-3', '2.16.840.1.113883.3.526.3.1334', 'Diagnostic Study, Performed: Optic Disc Exam for Structural Abnormalities', 'lionic'),
//('92004', '2.16.840.1.113883.3.526.3.1285', 'Encounter, Performed: Ophthalmological Services', 'cpt'),
//('92014', '2.16.840.1.113883.3.526.3.1285', 'Encounter, Performed: Ophthalmological Services', 'cpt'),
//('99328', '2.16.840.1.113883.3.464.1003.101.12.1014', 'Encounter, Performed: Care Services in Long-Term Residential Facility', 'cpt'),
//('112963003', '2.16.840.1.113883.3.526.3.1411', 'Procedure, Performed: Cataract Surgery', 'snomed'),
//('312904009', '2.16.840.1.113883.3.526.3.327', 'Diagnosis: Diabetes', 'snomed')";

//imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>MH &gt; Update 2 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>MH &gt; Update 2 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 2 (MH)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>