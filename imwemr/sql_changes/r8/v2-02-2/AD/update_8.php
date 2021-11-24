<?php
/*
 *------------------------------------------------------------
 * Create Table For Admin=>Billing=>CCDA SFTP Credentials Tab
 *------------------------------------------------------------
 *
 * This section needs to create to save the CCDA SFTP Credentials
 *
 */
$ignoreAuth = true;

include("../../../../config/globals.php");

$msg_info=array();

$sql="
	CREATE TABLE 
		IF NOT EXISTS 
		`ccda_sftp_credentials` 
		(
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`ccda_host_name` varchar(255) NOT NULL,
			`port_number` varchar(30) NOT NULL,
			`ccda_sftp_username` varchar(255) NOT NULL,
			`ccda_sftp_password` varchar(255) NOT NULL,
			`operator` int(11) NOT NULL,
			`date_time` datetime NOT NULL,
			`ccda_directory_path` varchar(255) NOT NULL,
			PRIMARY KEY (`id`)
		) 
	ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 8 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 8 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 8</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>