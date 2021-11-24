<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = array();
$msg_info = array();

$q[] = "CREATE TABLE `ibra_case` (
  `id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL,
  `json_input` text NOT NULL,
  `ibra_resonse` text NOT NULL,
  `cr_time` datetime NOT NULL,
  `provider` int(10) NOT NULL,
  `del_by` int(10) NOT NULL,
  `no_case_created` int(1) NOT NULL
) ENGINE=MyISAM;
";

$q[] = "ALTER TABLE `ibra_case`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);";
  
$q[] = "ALTER TABLE `ibra_case`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
 $q[] = "CREATE TABLE `ibra_token` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `create_time` datetime NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=MyISAM";

 $q[] = "ALTER TABLE `ibra_token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`);";
  
 $q[] = "ALTER TABLE `ibra_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
  
  $q[] = "CREATE TABLE `ibra_call_log` (
  `id` int(10) NOT NULL,
  `json_input` text NOT NULL,
  `req_header` text NOT NULL,
  `response` text NOT NULL,
  `dt_time` datetime NOT NULL
) ENGINE=MyISAM";

$q[] = "ALTER TABLE `ibra_call_log`
  ADD PRIMARY KEY (`id`);";
  
  $q[] = "ALTER TABLE `ibra_call_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";
  
  $q[] = "ALTER TABLE ibra_case ADD form_id INT(10) NOT NULL, 
		ADD site varchar(7) NOT NULL, DROP json_input, DROP ibra_resonse ";

foreach($q as $qry){
	imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 50  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 50  run successfully!</b>";
    $color = "green";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 50 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>