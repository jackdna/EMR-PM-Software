<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="ALTER TABLE `users` ADD `view_all_provider_financials` INT( 1 ) NOT NULL  DEFAULT '1' COMMENT 'If value=1, specific report will display result for all physicians. If 0 then report will display only result for themself in case of Group of Physicians, Technicians and Nurse. In case of other user groups, report will display result for selected physicians or for all',
ADD `provider_financials` TEXT NOT NULL COMMENT 'Linked to column  view_all_provider_financials. In case of other group of users ids of selected physicians stored.'";

imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 6  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 6  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
