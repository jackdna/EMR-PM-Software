<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();

//if(!constant('REVIEWINC_SUBMIT')) die('ReviewInc Submission not defined');

$res1 = imw_query("CREATE TABLE `review_inc_logs` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `patient_id` int(11) NOT NULL,
		  `sch_id` int(11) NOT NULL,
		  `mode` varchar(1) NOT NULL,
		  `data_sent` text NOT NULL,
		  `response` varchar(255) NOT NULL,
		  `sentdate` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
if($res1 && imw_num_rows($res1)==0){
	$msg_info[] = 'New compliance Report added - ACI 2019.';
}else{$msg_info[] = imw_error();}


?>
<html>
<head>
<title>Release 8 Updates 9 (ReviewInc)</title>
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