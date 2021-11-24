<?php 

$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="
CREATE TABLE `era_rules` (
  `era_rule_id` int(11) NOT NULL,
  `era_trans_method` varchar(50) NOT NULL,
  `era_cas_code` text NOT NULL,
  `entered_date_time` datetime NOT NULL,
  `entered_by` int(11) NOT NULL,
  `del_status` int(2) NOT NULL,
  `del_date_time` datetime NOT NULL,
  `del_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
imw_query($sql) or $msg_info[] = imw_error();


$sql="
ALTER TABLE `era_rules`
  ADD PRIMARY KEY (`era_rule_id`);
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `era_rules`
  MODIFY `era_rule_id` int(11) NOT NULL AUTO_INCREMENT;
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
CREATE TABLE `era_rules_log` (
  `era_rules_log_id` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `mod_date_time` datetime NOT NULL,
  `era_rule_id` int(11) NOT NULL,
  `era_trans_method` varchar(50) NOT NULL,
  `era_cas_code` text NOT NULL,
  `entered_date_time` datetime NOT NULL,
  `entered_by` int(11) NOT NULL,
  `del_status` int(2) NOT NULL,
  `del_date_time` datetime NOT NULL,
  `del_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `era_rules_log`
  ADD PRIMARY KEY (`era_rules_log_id`);
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `era_rules_log`
  MODIFY `era_rules_log_id` int(11) NOT NULL AUTO_INCREMENT;
";
imw_query($sql) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 100  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 100  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 100</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>