<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$qry="
CREATE TABLE `log_change_prvlgs` (
  `id` int(10) NOT NULL,
  `op_id` int(10) NOT NULL,
  `op_tm` datetime NOT NULL,
  `access_pri` text NOT NULL,
  `groups_prevlgs_id` int(11) NOT NULL,
  `effcted_uids` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";
$rs=imw_query($qry);

$qry="
ALTER TABLE `log_change_prvlgs`
  ADD PRIMARY KEY (`id`);
";
$rs=imw_query($qry);

$qry="
ALTER TABLE `log_change_prvlgs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";
$rs=imw_query($qry);


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 119 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 119 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 119</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
