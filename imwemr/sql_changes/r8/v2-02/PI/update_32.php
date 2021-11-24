<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

$sql1[] = "UPDATE languages SET lang_name = 'Bokml, Norwegian; Norwegian Bokml' WHERE lang_id = 322 ";
$sql1[] = "UPDATE languages SET lang_name = 'Provenal, Old (to 1500);Occitan, Old (to 1500)' WHERE lang_id = 359 ";
$sql1[] = "UPDATE languages SET lang_name = 'Volapk' WHERE lang_id = 470 ";

foreach($sql1 as $sql){imw_query($sql) or $msg_info[] = imw_error();}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 32 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 32 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 32</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>Update languages containing special characters </h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>

</body>
</html>