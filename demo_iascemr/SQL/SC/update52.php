<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");


$sql = "
CREATE TABLE `history_physicial_clearance` (
  `history_physicial_id` int(11) NOT NULL AUTO_INCREMENT,
  `confirmation_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `form_status` varchar(255) NOT NULL,
  `date_of_h_p` date NOT NULL,
  `malignanyHyperthermia` varchar(255) NOT NULL,
  `malignanyHyperthermiaDesc` text NOT NULL,
  `typicalResponse` varchar(255) NOT NULL,
  `typicalResponseDesc` text NOT NULL,
  `muscleHistory` varchar(255) NOT NULL,
  `muscleHistoryDesc` text NOT NULL,
  `surgicalHistory` varchar(255) NOT NULL,
  `surgicalHistoryDesc` text NOT NULL,
  `preAdmTest` varchar(255) NOT NULL,
  `preAdmTestDesc` text NOT NULL,
  `proposedAnesthetic` varchar(255) NOT NULL,
  `proposedAnestheticDesc` text NOT NULL,
  `proposedAnestheticGA` varchar(255) NOT NULL,
  `proposedAnestheticMAC` varchar(255) NOT NULL,
  `proposedAnestheticLOC` varchar(255) NOT NULL,
  `outPtSurgery` varchar(255) NOT NULL,
  `outPtSurgeryDesc` text NOT NULL,
  `changesInHp` varchar(255) NOT NULL,
  `changesInHpDesc` text NOT NULL,
  `general` varchar(255) NOT NULL,
  `generalDesc` text NOT NULL,
  `looseTeeth` varchar(255) NOT NULL,
  `looseTeethDesc` text NOT NULL,
  `heent` varchar(255) NOT NULL,
  `heentDesc` text NOT NULL,
  `heart` varchar(255) NOT NULL,
  `heartDesc` text NOT NULL,
  `lungs` varchar(255) NOT NULL,
  `lungsDesc` text NOT NULL,
  `regional` varchar(255) NOT NULL,
  `regionalDesc` text NOT NULL,
  `otherHistory` text NOT NULL,
  `signNurseId` int(11) NOT NULL,
  `signNurseFirstName` varchar(255) NOT NULL,
  `signNurseMiddleName` varchar(255) NOT NULL,
  `signNurseLastName` varchar(255) NOT NULL,
  `signNurseStatus` varchar(255) NOT NULL,
  `signNurseDateTime` datetime NOT NULL,
  `resetDateTime` datetime NOT NULL,
  `resetBy` int(11) NOT NULL,
  `save_date_time` datetime NOT NULL,
  `save_operator_id` int(11) NOT NULL,
  PRIMARY KEY (`history_physicial_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `left_navigation_forms`  ADD `history_physical_form` VARCHAR(5) NOT NULL DEFAULT 'true' AFTER `pre_op_health_ques_form`";
$row = imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 52 run OK";

?>

<html>
<head>
<title>Update 52</title>
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







