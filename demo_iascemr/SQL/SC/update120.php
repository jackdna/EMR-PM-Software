<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `users` ADD  `initial` VARCHAR( 10 ) NOT NULL AFTER  `lname`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE `narcotics_data_tbl` (
	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
	  `confirmation_id` int(11) NOT NULL,
	  `medicine_name` varchar(255) NOT NULL,
	  `quantity` decimal(10,2) NOT NULL,
	  `user_id` int(11) NOT NULL,
	  `user_initial` varchar(10) NOT NULL,
	  `user_fname` varchar(255) NOT NULL,
	  `user_mname` varchar(255) NOT NULL,
	  `user_lname` varchar(255) NOT NULL,
	  `created_date` datetime NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 120 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 120 Success.</b><br>".$message;
	$color = "green";			
}

?>
<html>
<head>
<title>Update 120</title>
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