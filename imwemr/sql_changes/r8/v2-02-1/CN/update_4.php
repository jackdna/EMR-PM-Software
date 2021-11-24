<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

$s = array();
//$s[] = "ALTER TABLE chart_macula ADD modi_note_maculaArr TEXT NOT NULL AFTER wnl_value_Macula; ";
$s[] = "ALTER TABLE chart_periphery ADD modi_note_periArr TEXT NOT NULL AFTER wnl_value_Peri; ";
$s[] = "ALTER TABLE chart_blood_vessels ADD modi_note_bvArr TEXT NOT NULL AFTER wnl_value_BV; ";
$s[] = "ALTER TABLE chart_periphery ADD periNotExamined INT(2) NOT NULL AFTER modi_note_periArr, ADD peri_ne_eye VARCHAR(20) NOT NULL AFTER periNotExamined;";
if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
	//$s[] = "ALTER TABLE chart_macula_archive ADD modi_note_maculaArr TEXT NOT NULL AFTER wnl_value_Macula;";
	$s[] = "ALTER TABLE chart_periphery_archive ADD modi_note_periArr TEXT NOT NULL AFTER wnl_value_Peri; ";
	$s[] = "ALTER TABLE chart_blood_vessels_archive ADD modi_note_bvArr TEXT NOT NULL AFTER wnl_value_BV; ";
	$s[] = "ALTER TABLE chart_periphery_archive ADD periNotExamined INT(2) NOT NULL AFTER modi_note_periArr, ADD peri_ne_eye VARCHAR(20) NOT NULL AFTER periNotExamined;";
}

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 4  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 4  run successfully!</b>";
    $color = "green";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 3</title>
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
