<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$sql = '
ALTER TABLE `chart_retinal_exam` CHANGE `modi_note_vitreousArr` `modi_note_retinalArr` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
ALTER TABLE `chart_vitreous` CHANGE `modi_note_retinalArr` `modi_note_vitreousArr` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
';
$result = imw_query($sql) or $msg_info[] = imw_error();



?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 70</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>