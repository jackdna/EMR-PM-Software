<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$q[] = 'CREATE TABLE `chart_save_log` (
  `id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `dttime` datetime NOT NULL,
  `section` varchar(20) NOT NULL,
  `logged_user_type` int(2) NOT NULL,
  `as_pln` mediumtext NOT NULL,
  `sess_id` varchar(150) NOT NULL,
  `finalized` int(1) NOT NULL
)';

$q[]='ALTER TABLE `chart_save_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `user_id` (`user_id`);';
  
 $q[]='ALTER TABLE `chart_save_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT'; 

  
foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 36 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 36 completed successfully. </b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 36</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>