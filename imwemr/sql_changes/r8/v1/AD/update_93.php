<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


$q[] = "ALTER TABLE  `reports_crone_jobs` 
ADD  `sftp_address` VARCHAR( 255 ) NOT NULL ,
ADD  `sftp_user` VARCHAR( 150 ) NOT NULL ,
ADD  `sftp_password` VARCHAR( 200 ) NOT NULL ,
ADD  `sftp_directory` VARCHAR( 200 ) NOT NULL,
ADD  `sftp_port` VARCHAR( 100 ) NOT NULL";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 93  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 93  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 93</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>