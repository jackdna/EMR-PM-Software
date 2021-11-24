<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

$sql1[] = "ALTER TABLE resp_party ADD licenseDate DATETIME NULL;";
$sql1[] = "ALTER TABLE resp_party ADD licenseComments TEXT NULL;";
$sql1[] = "ALTER TABLE resp_party ADD licenseOperator INT DEFAULT 0 NOT NULL;";

foreach($sql1 as $sql){imw_query($sql) or $msg_info[] = imw_error();}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 1 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>License Related columns for Responsible party</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>

</body>
</html>