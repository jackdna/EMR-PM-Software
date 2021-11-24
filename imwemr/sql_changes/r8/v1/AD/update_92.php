<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


$q[] = "CREATE TABLE  `slot_procedures_linked_op` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ref_id` INT NOT NULL ,
`proc_id` INT NOT NULL ,
`linked_op` VARCHAR( 100 ) NOT NULL
) ENGINE = MYISAM ;";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 92  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 92  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 92</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>