<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `surgerycenter` ADD `anes_mallampetti_score` INT( 11 ) NOT NULL ;";
$sql[] = "ALTER TABLE `localanesthesiarecord` ADD `mallampetti_score` VARCHAR( 255 ) NOT NULL;";
$sql[] = "ALTER TABLE `anesthesia_profile_tbl` ADD `mallampetti_score` VARCHAR( 255 ) NOT NULL;";
$sql[] = "ALTER TABLE `localanesthesiarecord` ADD `npo` VARCHAR( 255 ) NOT NULL;";
$sql[] = "ALTER TABLE `anesthesia_profile_tbl` ADD `npo` VARCHAR( 255 ) NOT NULL;";
$sql[] = "ALTER TABLE `anesthesia_profile_tbl` ADD `dentation` TEXT NOT NULL;";
$sql[] = "ALTER TABLE `localanesthesiarecord` ADD `dentation` TEXT NOT NULL;";
$sql[] = "CREATE TABLE `dentation` (`dentationId` int(10) NOT NULL AUTO_INCREMENT,
									  `name` varchar(255) NOT NULL,
									  `isDefault` int(11) NOT NULL,
									  PRIMARY KEY (`dentationId`)
									) ENGINE=MyISAM;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

imw_close($link); //CLOSE SURGERYCENTER CONNECTION
include('../../connect_imwemr.php'); // imwemr connection

$sql_idoc[] = "ALTER TABLE `pn_reports` ADD `sc_emr_laser_report_id` VARCHAR( 255 ) NOT NULL , ADD `sc_emr_injection_report_id` VARCHAR( 255 ) NOT NULL";
foreach($sql_idoc as $qry_idoc){
	imw_query($qry_idoc)or $msg_info[] = imw_error();
}

imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
include("../../common/conDb.php");  //SURGERYCENTER CONNECTION


$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 153 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 153 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 153</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>