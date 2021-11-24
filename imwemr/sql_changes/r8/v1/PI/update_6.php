<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[] = "CREATE TABLE `gender_code` (
  `gender_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `gender_name` varchar(20) NOT NULL,
  `gender_code` varchar(5) NOT NULL,
  `is_deleted` tinyint(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$sql[] = "INSERT INTO `gender_code` VALUES(1, 'Male', 'M', 0);";
$sql[] = "INSERT INTO `gender_code` VALUES(2, 'Female', 'F', 0);";
$sql[] = "INSERT INTO `gender_code` VALUES(3, 'Unknown', 'UNK', 0);";


foreach( $sql as $qry)
	imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 6 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 6 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 6 (PI)</title>
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