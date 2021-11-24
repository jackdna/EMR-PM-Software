<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1=" CREATE TABLE  `pre_nurse_alderate` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `confirmation_id` int(11) NOT NULL, `points_detail` text NOT NULL, `form_status` varchar(50) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
imw_query($sql1) or $msg_info[] = (imw_error());

$sql1=" CREATE TABLE  `post_nurse_alderate` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `confirmation_id` int(11) NOT NULL, `points_detail` text NOT NULL, `form_status` varchar(50) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
imw_query($sql1) or $msg_info[] = (imw_error());


$sql1=" ALTER TABLE  `left_navigation_forms` 
ADD  `pre_nurse_alderate_form` VARCHAR( 5 ) NOT NULL DEFAULT 'true' AFTER  `pre_op_nursing_form` ,
ADD  `post_nurse_alderate_form` VARCHAR( 5 ) NOT NULL DEFAULT 'true' AFTER  `post_op_nursing_form` ; ";
imw_query($sql1) or $msg_info[] = (imw_error());


$sql1=" CREATE TABLE `alderate_scoring_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
imw_query($sql1) or $msg_info[] = (imw_error());


$sql1=" INSERT INTO `alderate_scoring_categories` (`id`, `categoryName`) VALUES
(1, 'Activity'),
(2, 'Respiration'),
(3, 'Consciousness'),
(4, 'Circulation'),
(5, 'Color'); ";
imw_query($sql1) or $msg_info[] = (imw_error());


$sql1=" CREATE TABLE `alderate_scoring_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `assessment_point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
imw_query($sql1) or $msg_info[] = (imw_error());

$sql1=" INSERT INTO `alderate_scoring_questions` (`id`, `category_id`, `question`, `assessment_point`) VALUES
(1, 1, 'Able to move 4 extremities voluntarily on command', 2),
(2, 1, 'Able to move 2 extremities voluntarily on command', 1),
(3, 1, 'Able to move 0 extremities voluntarily on command', 0),
(4, 2, 'Able to breathe deeply and cough freely', 2),
(5, 2, 'Dyspnea or limited breathing', 1),
(6, 2, 'Apneic', 0),
(7, 3, 'Fully awake', 2),
(8, 3, 'Arousable on calling', 1),
(9, 3, 'Not responding', 0),
(10, 4, 'B/P +/-20% of preanesthetic level', 2),
(11, 4, 'B/P +/-20% to 50% of preanesthetic level', 1),
(12, 4, 'B/P +/-50% of preanesthetic level', 0),
(13, 5, 'Normal', 2),
(14, 5, 'Pale, dusky, blotchy, jaundiced, other cyanotic', 1),
(15, 5, 'Cyanotic', 0);
";
imw_query($sql1) or $msg_info[] = (imw_error());

// End Updates 21 Nov 2015




if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 76 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 76 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 76</title>
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