<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "CREATE TABLE `iportal_pghd_reqs` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `patient_id` int(10) NOT NULL,
  `pt_external_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pghd_req_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `demographics` text COLLATE utf8_unicode_ci NOT NULL,
  `medications` text COLLATE utf8_unicode_ci NOT NULL,
  `allergies` text COLLATE utf8_unicode_ci NOT NULL,
  `familyHistoryProblems` text COLLATE utf8_unicode_ci NOT NULL,
  `insurances` text COLLATE utf8_unicode_ci NOT NULL,
  `surgeries` text COLLATE utf8_unicode_ci NOT NULL,
  `approved_declined` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=approved, 2=declined',
  `created_on` datetime NOT NULL,
  `approved_by` int(10) NOT NULL DEFAULT '0',
  `operator` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `action_date` datetime NOT NULL,
  `demo_qry` text COLLATE utf8_unicode_ci NOT NULL,
  `med_qry` text COLLATE utf8_unicode_ci NOT NULL,
  `alrg_qry` text COLLATE utf8_unicode_ci NOT NULL,
  `prob_qry` text COLLATE utf8_unicode_ci NOT NULL,
  `ins_qry` text COLLATE utf8_unicode_ci NOT NULL,
  `surg_qry` text COLLATE utf8_unicode_ci NOT NULL
) ";
imw_query($sql2) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 7 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 7 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 7</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
