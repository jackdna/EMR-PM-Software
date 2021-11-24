<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/condb.php");

$query1="
CREATE TABLE `zip_codes` (
  `zip_id` int(10) NOT NULL auto_increment,
  `zip_code` varchar(255) collate latin1_general_ci NOT NULL,
  `city` varchar(255) collate latin1_general_ci NOT NULL,
  `state_abb` varchar(255) collate latin1_general_ci NOT NULL,
  `state` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`zip_id`)
) ENGINE=MyISAM
";
$result1 = @imw_query($query1);

/********************#zip_codes ************/

$pathVar =  $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];
$pathVar = str_replace(".php",".csv",$pathVar);
$filename=$pathVar;
$query = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE `zip_codes` FIELDS TERMINATED BY ',' IGNORE 0 LINES ";
$result = imw_query($query)or die(imw_error());

$msg_info[] = "Zip codes imported"; 

?> 	
	
<html>
<head>
<title>Mysql Updates After Launch</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info)."<Br>");?></font>
 	
<?php
}
 ?> 

</body>
</html>
