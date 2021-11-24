<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("ALTER TABLE copay_policies ADD checkin_on_done TINYINT NOT NULL;") or $msg_info[] = imw_error();

if( $rs ) 
{
    
    $rs = imw_query("Update copay_policies Set checkin_on_done = ".(int)constant('CHECKIN_ON_DONE')." WHERE policies_id = 1 ") or $msg_info[] = imw_error();

}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 4  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 4 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>