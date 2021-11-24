<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

        
$qry=" CREATE TABLE IF NOT EXISTS `user_messages_folder` (
  `folder_id` int(10) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `folder_status` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `provider_id` int(10) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`folder_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ";
imw_query($qry) or $msg_info[] = imw_error();

$qry1="ALTER TABLE `user_messages` ADD `saved_folder_id` INT( 10 ) NOT NULL DEFAULT '0'";
imw_query($qry1) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 69 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 69 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 69</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>