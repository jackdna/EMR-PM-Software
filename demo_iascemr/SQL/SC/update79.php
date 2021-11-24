<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `operatingroomrecords` CHANGE `nurseTitle` `nurseTitle` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE `operatingroomrecords` SET nurseTitle = '' WHERE form_status = '' "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="
CREATE TABLE `postop_nurse_checklist` (
  `postopNurseChecklistId` int(10) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`postopNurseChecklistId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="
CREATE TABLE `patient_postop_nurse_checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postOpNurseQuestionName` text NOT NULL,
  `postOpNurseOption` text NOT NULL,
  `confirmation_id` int(11) NOT NULL DEFAULT '0',
  `patient_id` int(11) NOT NULL DEFAULT '0',
  `checkListTemplateId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 "; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="
CREATE TABLE `postopnursequestion` (
  `postOpNurseQuestionId` int(10) NOT NULL AUTO_INCREMENT,
  `specialty_id` int(11) NOT NULL,
  `postOpNurseQuestionName` text COLLATE latin1_general_ci NOT NULL,
  `postOpNurseCatId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`postOpNurseQuestionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="
CREATE TABLE `postopnursecategory` (
  `categoryId` int(11) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="
CREATE TABLE `postopnursequestionadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) NOT NULL,
  `postOpNurseQuestionName` text NOT NULL,
  `postOpNurseOption` text NOT NULL,
  `confirmation_id` int(11) NOT NULL DEFAULT '0',
  `ascId` bigint(11) NOT NULL DEFAULT '0',
  `patient_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 "; 
imw_query($sql1)or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 79 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 79 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 79</title>
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