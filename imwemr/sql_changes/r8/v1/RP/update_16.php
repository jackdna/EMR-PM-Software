<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();

$res1 = imw_query("CREATE TABLE `testimonial_tree_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` text NULL,
  `sch_id` text NULL,
  `data_sent` text NOT NULL,
  `response` varchar(255) NOT NULL,
  `logtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
)");

if($res1 && imw_num_rows($res1)==0){
	$msg_info[] = 'New Table added for Testimonial Tree Logs (IM-2263)';
}else{$msg_info[] = imw_error();}


?>
<html>
<head>
<title>Release 8 Updates 16 (Testimonial Tree Logs table)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>