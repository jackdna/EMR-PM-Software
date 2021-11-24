<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql = [];
$sql[] = "ALTER TABLE pt_docs_collection_letters ADD sent_to TINYINT NOT NULL COMMENT '0=Patient\r\n1=Insurance' AFTER delete_status;";

foreach($sql as $query)
	imw_query($query) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>V2.02.1 - Update 2 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>V2.02.1 - Update 2 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>V2.02.1 - Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>