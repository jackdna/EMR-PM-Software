<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array(); 
$chkQry = imw_query("select sx_plan_dos from chart_sx_plan_sheet");
if(!$chkQry) $q[] = "ALTER TABLE  `chart_sx_plan_sheet` ADD  `sx_plan_dos` DATE NULL AFTER `id`";

$q[] = "ALTER TABLE  `chart_sx_plan_sheet` ADD  `pe_pupil_dilated` VARCHAR( 250 ) NOT NULL ";
$q[] = "ALTER TABLE  `chart_sx_plan_sheet` ADD  `lens_sle_summary` VARCHAR( 250 ) NOT NULL ";

foreach($q as $sql){
    $r = imw_query($sql) or $msg_info[]=imw_error();
}
if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 6  run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 6  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 6 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>