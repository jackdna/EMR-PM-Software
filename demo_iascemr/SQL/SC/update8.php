<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql = "CREATE TABLE IF NOT EXISTS `qualitymeasures` (
  `qualityId` bigint(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `confirmation_id` int(11) NOT NULL,
  `qualityName` varchar(255) NOT NULL,
  `qualityStatus` varchar(255) NOT NULL,
  PRIMARY KEY (`qualityId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
$row = imw_query($sql) or $msg_info[] = imw_error();


$sql = "CREATE TABLE `qualitymeasuresadmin` (
`qualityMeasuresId` BIGINT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`status` VARCHAR( 100 ) NOT NULL 
) ENGINE = MYISAM ;";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 8 run OK";

?>

<html>
<head>
<title>Mysql Updates For Create Table in surgical_check_list</title>
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







