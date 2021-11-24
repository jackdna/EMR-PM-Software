<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE `sage_sftp_credentials` (
    `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sage_host_name` varchar(255) NOT NULL,
    `port_number` varchar(30) NOT NULL,
    `sage_sftp_username` varchar(255) NOT NULL,
    `sage_sftp_password` varchar(255) NOT NULL,
    `operator` int(11) NOT NULL,
    `date_time` datetime NOT NULL 
) ";
imw_query($sql) or $msg_info[] = imw_error();

imw_query("ALTER TABLE sage_sftp_credentials ADD sage_directory_path VARCHAR( 255 ) NOT NULL") or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 150 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 150 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 150</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>