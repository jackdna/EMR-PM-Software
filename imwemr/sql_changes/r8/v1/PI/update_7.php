<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[] = "ALTER TABLE `patient_data` ADD `mname_br` VARCHAR( 50 ) NOT NULL COMMENT 'Birth name' AFTER `mname`";

foreach( $sql as $qry)
	imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 7 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 7 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 7 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>