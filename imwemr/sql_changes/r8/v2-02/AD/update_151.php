<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'reportenctrans_transmethod' ";
$row=sqlQuery($sql);

if($row == false){
    $sql="CREATE INDEX reportenctrans_transmethod ON report_enc_trans(trans_method);";
    imw_query($sql)or $msg_info[] = imw_error();
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 151  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 151  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 151 -Index creating</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
