<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql = [];
$sql[] = "ALTER TABLE patient_charge_list_details ADD modifier_id4 INT( 4 ) NOT NULL AFTER modifier_id3;";
$sql[] = "ALTER TABLE procedureinfo ADD modifier4 TEXT NOT NULL AFTER modifier3;";
$sql[] = "ALTER TABLE procedureinfo ADD modifier4MenuString LONGTEXT NOT NULL AFTER modifier3MenuString;";
$sql[] = "ALTER TABLE cpt_fee_tbl ADD mod4 VARCHAR( 255 ) NOT NULL AFTER mod3;";
$sql[] = "ALTER TABLE report_enc_detail ADD mod_id4 INT( 11 ) NOT NULL AFTER mod_id3;";

foreach($sql as $query)
	imw_query($query) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>V2.02.1 - Update 1 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>V2.02.1 - Update 1 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>V2.02.1 - Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>