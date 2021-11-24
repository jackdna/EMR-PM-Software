<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


$q[] = "ALTER TABLE `cpt_fee_tbl` ADD `unit_of_measure` VARCHAR( 50 ) NOT NULL , ADD `measurement` VARCHAR( 50 ) NOT NULL ";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 95  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 95  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 95</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>