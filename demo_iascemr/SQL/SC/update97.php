<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

#IMPORT FACILITIES FROM IDOC
?>
<?php
set_time_limit(900);

//reconnect to surgery center database
require("../../common/conDb.php");

//$sql1="ALTER TABLE `surgerycenter`  ADD `group_institution` INT(11) NOT NULL,  ADD `group_anesthesia` INT(11) NOT NULL,  ADD `group_practice` INT(11) NOT NULL"; 
//imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `facility_tbl` ADD  `fac_logo` LONGBLOB NOT NULL ,
ADD  `fac_logoName` VARCHAR( 255 ) NOT NULL ,
ADD  `fac_logoType` VARCHAR( 255 ) NOT NULL ,
ADD  `fac_group_institution` INT NOT NULL ,
ADD  `fac_group_anesthesia` INT NOT NULL ,
ADD  `fac_group_practice` INT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 97 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 97 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 97</title>
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