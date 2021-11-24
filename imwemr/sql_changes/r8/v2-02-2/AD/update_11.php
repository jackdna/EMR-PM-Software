<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("
ALTER TABLE `previous_statement` ADD `email_sent` VARCHAR( 10 ) NOT NULL ,
ADD `email_status` VARCHAR( 10 ) NOT NULL ,
ADD `email_operator` INT NOT NULL,
ADD `email_date_time` DATETIME NOT NULL
") or $msg_info[] = imw_error();

$rs=imw_query("ALTER TABLE `direct_messages` ADD `email_status` VARCHAR( 10 ) NOT NULL")  or $msg_info[] = imw_error();

$rs=imw_query("ALTER TABLE `pt_docs_collection_letters` 
ADD `email_sent` VARCHAR( 10 ) NOT NULL,
ADD `email_status` VARCHAR( 10 ) NOT NULL")  or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 11  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 11  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 11</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>