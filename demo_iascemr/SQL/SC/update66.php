<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `iolink_consent_filled_form`  ADD `consent_save_date_time` DATETIME NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="
CREATE TABLE `finalize_history` (
  `finalize_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_confirmation_id` int(11) NOT NULL,
  `finalize_action` varchar(20) NOT NULL COMMENT 'Unfinalize|finalize',
  `finalize_action_script` varchar(10) NOT NULL COMMENT 'auto|manual',
  `finalize_action_type` varchar(20) NOT NULL COMMENT 'revised|original',
  `finalize_action_user_id` int(11) NOT NULL,
  `finalize_action_datetime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`finalize_history_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";
imw_query($sql1)or $msg_info[] = imw_error();



if(imw_error())
{
	$msg_info[] = "<br><br><b>Update 66 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 66 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 66</title>
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