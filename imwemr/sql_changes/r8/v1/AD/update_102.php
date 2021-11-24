<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql7="ALTER TABLE `groups_new` ADD `THCICSubmitterId` VARCHAR( 150 ) NOT NULL  ";
imw_query($sql7) or $msg_info[] = imw_error();

$sql8="ALTER TABLE `pos_facilityies_tbl` ADD `thcic_id` VARCHAR( 50 ) NOT NULL ";
imw_query($sql8) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 102  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 102  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 102</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>