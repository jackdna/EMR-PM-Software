<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "ALTER TABLE statement_template ADD email_subject VARCHAR( 250 ) NOT NULL ,ADD email_body LONGTEXT NOT NULL ";
imw_query($sql2) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 2 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 2 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
 
