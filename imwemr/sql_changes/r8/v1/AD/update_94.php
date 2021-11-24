<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


$q[] = "ALTER TABLE  `reports_crone_jobs` 
ADD  `output_option` VARCHAR(100) NOT NULL";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 94  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 94  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 94</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>