<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql18="CREATE TABLE IF NOT EXISTS `ins_cpt_alert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ins_id` int(11) NOT NULL,
  `cpt_code_id` text NOT NULL,
  `cpt_alert` text NOT NULL,
  `entered_by` int(11) NOT NULL,
  `entered_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  `del_by` int(11) NOT NULL,
  `del_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ";
imw_query($sql18) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 107 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 107 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 107</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>