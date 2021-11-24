<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql="CREATE TABLE IF NOT EXISTS `manufacturer_lens_category` (
  `manufacturerLensCategoryId` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`manufacturerLensCategoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$manArr = array('Alcon','Starr','AMO','Pharmacia','Eyeonics','B&L','Hoya');
foreach($manArr as $key => $manufacturer) {
	$chkNameQry = imw_query("SELECT name FROM manufacturer_lens_category WHERE name='".$manufacturer."'") or $msg_info[] = imw_error();
	if(imw_num_rows($chkNameQry)<=0) {
		echo '<br>'.$insManQry = "INSERT INTO manufacturer_lens_category SET name='".$manufacturer."'";
		$insManRes = imw_query($insManQry) or $msg_info[] = imw_error();				
	}
}


$sql = "CREATE TABLE IF NOT EXISTS `manufacturer_lens_brand` (
  `lensBrandId` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `catId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lensBrandId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `operatingroomrecords` ADD `lensBrand` TEXT NOT NULL AFTER `manufacture`";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `iolink_iol_manufacturer` ADD `lensBrand` TEXT NOT NULL AFTER `manufacture`";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 2 run OK";

?>

<html>
<head>
<title>Mysql Updates For Lens Brand in iOlink/EMR</title>
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







