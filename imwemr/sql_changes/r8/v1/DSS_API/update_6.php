<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1 = "ALTER TABLE `dss_code_dictionary` ADD `lexIen` VARCHAR(50) NOT NULL;";
imw_query($sql1) or $error[] = imw_error();

if(count($error)>0)
{
  $error[] = "<br><br><b>Update 6 Failed!</b>";
  $color = "red";
}
else
{
  $error[] = "<br><br><b>Update 6 Success.</b>";
  $color = "green"; 
}
?>

<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>