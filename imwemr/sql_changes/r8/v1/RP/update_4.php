<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
imw_query("UPDATE `custom_reports` SET `template_fields` = 'a:26:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"10\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"1\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:9:\"analytics\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:15:\"filing_provider\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:18:\"crediting_provider\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:9:\"operators\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:14:\"summary_detail\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:3:\"dos\";s:5:\"value\";s:1:\"1\";}i:11;a:2:{s:4:\"name\";s:3:\"dor\";s:5:\"value\";s:1:\"1\";}i:12;a:2:{s:4:\"name\";s:3:\"dot\";s:5:\"value\";s:1:\"1\";}i:13;a:2:{s:4:\"name\";s:9:\"ins_group\";s:5:\"value\";s:1:\"1\";}i:14;a:2:{s:4:\"name\";s:12:\"ins_carriers\";s:5:\"value\";s:1:\"1\";}i:15;a:2:{s:4:\"name\";s:9:\"ins_types\";s:5:\"value\";s:1:\"1\";}i:16;a:2:{s:4:\"name\";s:11:\"icd10_codes\";s:5:\"value\";s:1:\"1\";}i:17;a:2:{s:4:\"name\";s:3:\"cpt\";s:5:\"value\";s:1:\"1\";}i:18;a:2:{s:4:\"name\";s:14:\"grpby_facility\";s:5:\"value\";s:1:\"1\";}i:19;a:2:{s:4:\"name\";s:15:\"grpby_physician\";s:5:\"value\";s:1:\"1\";}i:20;a:2:{s:4:\"name\";s:15:\"grpby_operators\";s:5:\"value\";s:1:\"1\";}i:21;a:2:{s:4:\"name\";s:22:\"output_actvity_summary\";s:5:\"value\";s:1:\"1\";}i:22;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:23;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}i:24;a:2:{s:4:\"name\";s:7:\"cpt_cat\";s:5:\"value\";s:1:\"1\";}i:25;a:2:{s:4:\"name\";s:15:\"grpby_procedure\";s:5:\"value\";s:1:\"1\";}}' WHERE `custom_reports`.`id` = 10;") or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 2 Failed!</b>";
	$color = "red";
}
else{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 2 successfull</b>";
	$color = "green";

}
?>
<html>
<head>
<title>Release 8 Updates 4 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>