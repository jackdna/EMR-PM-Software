<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$msg_info = array();

$q[] = " ALTER TABLE  `ccda_export_schedule` ADD  `zip_encrypt` INT( 11 ) NOT NULL COMMENT  '1 encrypt the zip file', ADD INDEX zip_encrypt( zip_encrypt );";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 10  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 10  run successfully!</b>";
    $color = "green";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 10 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>